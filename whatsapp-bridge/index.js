/**
 * WhatsApp Bridge — Display Lanka Admin
 *
 * Companion Node.js service using @whiskeysockets/baileys (WhatsApp Web multi-device protocol).
 * Persists auth session to disk so restarts don't require re-scanning.
 *
 * Endpoints:
 *   GET  /qr              — Current QR code as base64 data URL (while pairing)
 *   GET  /status          — { connected, phone_number, state }
 *   POST /send-message    — { to, message } — sends via linked account
 *   POST /logout          — Unlinks session
 *
 * Laravel webhook push:
 *   Pushes connection.update and incoming messages to LARAVEL_WEBHOOK_URL
 *   with X-Bridge-Secret header for verification.
 *
 * Usage:
 *   node index.js
 *   (or via PM2: pm2 start index.js --name whatsapp-bridge)
 */

import express from 'express';
import cors from 'cors';
import axios from 'axios';
import qrcode from 'qrcode';
import pino from 'pino';
import {
    makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
    fetchLatestBaileysVersion,
    makeCacheableSignalKeyStore,
} from '@whiskeysockets/baileys';
import { readFileSync, existsSync, rmSync, readdirSync } from 'fs';

// ─── Config ──────────────────────────────────────────────────────────────────
const PORT              = parseInt(process.env.BRIDGE_PORT    || '3000', 10);
const LARAVEL_WEBHOOK   = process.env.LARAVEL_WEBHOOK_URL     || 'http://127.0.0.1:8000/whatsapp/bridge-webhook';
const BRIDGE_SECRET     = process.env.BRIDGE_SECRET           || 'bridge-shared-secret';
const AUTH_DIR          = process.env.AUTH_DIR                || './auth_info_baileys';
const logger            = pino({ level: process.env.LOG_LEVEL || 'warn' });

// ─── State ────────────────────────────────────────────────────────────────────
let sock         = null;
let currentQR    = null;       // raw QR string for qrcode library
let qrDataUrl    = null;       // base64 PNG data URL
let qrExpiresAt  = null;
let connectionState = {
    connected:    false,
    phone_number: null,
    state:        'disconnected',
};

// ─── Laravel webhook helper ───────────────────────────────────────────────────
async function pushToLaravel(event, payload) {
    try {
        await axios.post(LARAVEL_WEBHOOK, { event, payload }, {
            headers: {
                'X-Bridge-Secret': BRIDGE_SECRET,
                'Content-Type':    'application/json',
                'Accept':          'application/json',
            },
            timeout: 5000,
        });
    } catch (err) {
        logger.warn({ event, err: err.message }, 'Laravel webhook push failed');
    }
}

// ─── QR generator ────────────────────────────────────────────────────────────
async function generateQrDataUrl(rawQr) {
    return await qrcode.toDataURL(rawQr, { margin: 1, width: 280 });
}

// ─── Baileys socket ───────────────────────────────────────────────────────────
async function startSocket() {
    const { state: authState, saveCreds } = await useMultiFileAuthState(AUTH_DIR);
    const { version } = await fetchLatestBaileysVersion();

    connectionState.state = 'connecting';

    sock = makeWASocket({
        version,
        auth: {
            creds:  authState.creds,
            keys:   makeCacheableSignalKeyStore(authState.keys, logger),
        },
        logger,
        printQRInTerminal: false,
        // Don't mark received messages as read
        markOnlineOnConnect: false,
    });

    // Save credentials whenever they change
    sock.ev.on('creds.update', saveCreds);

    // Handle QR codes (pairing phase)
    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            currentQR   = qr;
            qrDataUrl   = await generateQrDataUrl(qr);
            qrExpiresAt = Date.now() + 20_000;   // Baileys QR expires in ~20s
            logger.info('New QR generated');
        }

        if (connection === 'open') {
            const jid   = sock.user?.id ?? '';
            const phone = jid.split(':')[0].split('@')[0];

            connectionState = {
                connected:    true,
                phone_number: phone || null,
                state:        'connected',
            };

            // Clear QR now that we're connected
            currentQR = null;
            qrDataUrl = null;

            logger.info({ phone }, 'WhatsApp connected');
            await pushToLaravel('connection.update', {
                state:        'connected',
                phone_number: phone || null,
            });
        }

        if (connection === 'close') {
            const reason = lastDisconnect?.error?.output?.statusCode;
            const shouldReconnect = reason !== DisconnectReason.loggedOut;

            connectionState = {
                connected:    false,
                phone_number: null,
                state:        'disconnected',
            };

            await pushToLaravel('connection.update', {
                state:        'disconnected',
                phone_number: null,
                reason_code:  reason ?? null,
            });

            if (shouldReconnect) {
                logger.info({ reason }, 'Connection closed — reconnecting…');
                setTimeout(startSocket, 3000);
            } else {
                logger.info('Logged out — not reconnecting');
            }
        }
    });

    // Forward incoming messages to Laravel
    sock.ev.on('messages.upsert', async ({ messages, type }) => {
        if (type !== 'notify') return;

        for (const msg of messages) {
            if (msg.key.fromMe) continue;    // skip outbound echoes

            const from    = msg.key.remoteJid?.replace('@s.whatsapp.net', '').replace('@c.us', '');
            const text    = msg.message?.conversation
                         || msg.message?.extendedTextMessage?.text
                         || '';

            if (!from || !text) continue;

            await pushToLaravel('messages.upsert', {
                from:       '+' + from,
                text,
                timestamp:  msg.messageTimestamp,
                message_id: msg.key.id,
            });
        }
    });
}

// ─── Express app ─────────────────────────────────────────────────────────────
const app = express();
app.use(cors());
app.use(express.json());

// GET /qr — return current QR (base64 PNG) or 503 if no QR yet
app.get('/qr', async (_req, res) => {
    // If QR is expired or not available, return 503
    if (!qrDataUrl || !currentQR || (qrExpiresAt && Date.now() > qrExpiresAt)) {
        return res.status(503).json({ error: 'No QR code available. Already connected or pairing not started.' });
    }
    res.json({
        qr:        currentQR,
        image:     qrDataUrl,
        expires_at: qrExpiresAt,
    });
});

// GET /status — connection state
app.get('/status', (_req, res) => {
    res.json(connectionState);
});

// POST /send-message — { to, message }
app.post('/send-message', async (req, res) => {
    const { to, message } = req.body || {};

    if (!to || !message) {
        return res.status(422).json({ error: 'to and message are required' });
    }
    if (!connectionState.connected || !sock) {
        return res.status(503).json({ error: 'WhatsApp not connected' });
    }

    // Normalise to WhatsApp JID format
    const digits = to.replace(/\D/g, '');
    const jid    = digits + '@s.whatsapp.net';

    try {
        await sock.sendMessage(jid, { text: message });
        res.json({ success: true, to: jid });
    } catch (err) {
        logger.error({ err: err.message }, 'send-message failed');
        res.status(500).json({ error: err.message });
    }
});

// POST /logout — unlink session
app.post('/logout', async (_req, res) => {
    try {
        if (sock) {
            await sock.logout();
            sock = null;
        }

        // Remove persisted auth files
        if (existsSync(AUTH_DIR)) {
            const files = readdirSync(AUTH_DIR);
            for (const file of files) {
                rmSync(`${AUTH_DIR}/${file}`, { force: true });
            }
        }

        connectionState = { connected: false, phone_number: null, state: 'disconnected' };
        currentQR  = null;
        qrDataUrl  = null;

        await pushToLaravel('connection.update', { state: 'disconnected', phone_number: null });

        // Re-init so new QR can be shown immediately
        setTimeout(startSocket, 500);

        res.json({ success: true });
    } catch (err) {
        logger.error({ err: err.message }, 'logout failed');
        res.status(500).json({ error: err.message });
    }
});

// ─── Start ────────────────────────────────────────────────────────────────────
app.listen(PORT, () => {
    console.log(`\n🌿  WhatsApp Bridge running on http://127.0.0.1:${PORT}`);
    console.log(`    Laravel webhook → ${LARAVEL_WEBHOOK}`);
    console.log(`    Auth state      → ${AUTH_DIR}\n`);
});

startSocket();
