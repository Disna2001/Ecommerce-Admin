# PM2 ecosystem config — keeps the bridge running in production/staging
#
# Usage:
#   pm2 start ecosystem.config.cjs
#   pm2 save && pm2 startup

module.exports = {
    apps: [
        {
            name: 'whatsapp-bridge',
            script: 'index.js',
            cwd: __dirname,
            instances: 1,
            autorestart: true,
            watch: false,
            max_memory_restart: '300M',
            env: {
                BRIDGE_PORT:          '3000',
                LARAVEL_WEBHOOK_URL:  'http://127.0.0.1:8000/whatsapp/bridge-webhook',
                BRIDGE_SECRET:        'change_me_in_production',
                AUTH_DIR:             './auth_info_baileys',
                LOG_LEVEL:            'warn',
                NODE_ENV:             'production',
            },
        },
    ],
};
