const { app, BrowserWindow, Menu, ipcMain } = require('electron');
const path = require('path');
const fs = require('fs');
const net = require('net');
const { exec } = require('child_process');

let mainWindow;
const configPath = path.join(app.getPath('userData'), 'config.json');

// Default configurations
let config = {
  baseUrl: 'http://ecommerce.test',
  defaultView: '/admin',
};

// Load existing config if available
function loadConfig() {
  try {
    if (fs.existsSync(configPath)) {
      const data = fs.readFileSync(configPath, 'utf8');
      config = { ...config, ...JSON.parse(data) };
    }
  } catch (err) {
    console.error('Failed to load configurations:', err);
  }
}

// Save config to userData directory
function saveConfig(newConfig) {
  try {
    config = { ...config, ...newConfig };
    fs.writeFileSync(configPath, JSON.stringify(config, null, 2), 'utf8');
    return true;
  } catch (err) {
    console.error('Failed to save configurations:', err);
    return false;
  }
}

function createWindow() {
  mainWindow = new BrowserWindow({
    width: 1280,
    height: 800,
    minWidth: 800,
    minHeight: 600,
    icon: path.join(__dirname, 'icon.png'),
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
      contextIsolation: true,
      nodeIntegration: false,
    },
  });

  // Inject custom token into the User-Agent to allow backend context detection
  mainWindow.webContents.userAgent = mainWindow.webContents.userAgent + ' DisplayLankaDesktop';

  // Native application top menu bar configuration
  const menuTemplate = [
    {
      label: 'Navigation',
      submenu: [
        {
          label: 'Dashboard',
          accelerator: 'CmdOrCtrl+D',
          click: () => navigateTo('/admin'),
        },
        {
          label: 'POS Console',
          accelerator: 'CmdOrCtrl+P',
          click: () => navigateTo('/admin/pos'),
        },
        {
          label: 'Storefront',
          accelerator: 'CmdOrCtrl+S',
          click: () => navigateTo('/'),
        },
        { type: 'separator' },
        {
          label: 'App Configuration',
          accelerator: 'CmdOrCtrl+Comma',
          click: () => loadSettingsPage(),
        },
      ],
    },
    {
      label: 'View',
      submenu: [
        { role: 'reload' },
        { role: 'forceReload' },
        { role: 'toggleDevTools' },
        { type: 'separator' },
        { role: 'togglefullscreen' },
      ],
    },
    {
      label: 'Window',
      submenu: [{ role: 'minimize' }, { role: 'close' }],
    },
  ];

  const menu = Menu.buildFromTemplate(menuTemplate);
  Menu.setApplicationMenu(menu);

  // Load configured entry point
  loadConfig();
  launchInitialView();

  // If page loading fails, redirect to local configuration page with error message
  mainWindow.webContents.on('did-fail-load', (event, errorCode, errorDescription, validatedURL) => {
    // Ignore cancelled loading errors (which are normal for redirects)
    if (errorCode === -3 || errorCode === -2) return;
    
    console.warn(`Failed loading: ${validatedURL}. Code: ${errorCode}. Redirecting to setup...`);
    mainWindow.loadFile(path.join(__dirname, 'index.html'));
    
    // Once settings page loads, trigger error display
    mainWindow.webContents.once('did-finish-load', () => {
      mainWindow.webContents.send('connection-error', {
        url: validatedURL,
        description: errorDescription,
        code: errorCode,
      });
    });
  });
}

function navigateTo(targetPath) {
  if (!mainWindow) return;
  const targetUrl = config.baseUrl.replace(/\/$/, '') + targetPath;
  mainWindow.loadURL(targetUrl);
}

function loadSettingsPage() {
  if (!mainWindow) return;
  mainWindow.loadFile(path.join(__dirname, 'index.html'));
}

function launchInitialView() {
  if (!config.baseUrl) {
    loadSettingsPage();
  } else {
    navigateTo(config.defaultView);
  }
}

// IPC event handlers from renderer process
ipcMain.handle('get-config', () => {
  return config;
});

ipcMain.handle('save-config', (event, newConfig) => {
  const result = saveConfig(newConfig);
  if (result) {
    launchInitialView();
  }
  return result;
});

ipcMain.handle('clear-cache', async () => {
  if (!mainWindow) return false;
  try {
    await mainWindow.webContents.session.clearCache();
    await mainWindow.webContents.session.clearStorageData({
      storages: ['appcache', 'cookies', 'localstorage', 'shadercache', 'websql', 'indexdb']
    });
    return true;
  } catch (err) {
    console.error('Failed to clear app cache:', err);
    return false;
  }
});

ipcMain.handle('launch-view', () => {
  launchInitialView();
});

// Helper: send ESC/POS commands to network TCP/IP printer
function sendToNetworkPrinter(ip, port, dataBuffer) {
  return new Promise((resolve, reject) => {
    const socket = new net.Socket();
    socket.setTimeout(5000);

    socket.connect(port, ip, () => {
      socket.write(dataBuffer, () => {
        socket.end();
        resolve();
      });
    });

    socket.on('error', (err) => {
      socket.destroy();
      reject(err);
    });

    socket.on('timeout', () => {
      socket.destroy();
      reject(new Error('Connection to TCP network printer timed out'));
    });
  });
}

// Helper: send ESC/POS commands to Windows Spooler raw print queue via PowerShell
function sendToSpoolerPrinter(printerName, dataBuffer) {
  return new Promise((resolve, reject) => {
    const tempDir = app.getPath('temp');
    const tempBin = path.join(tempDir, `escpos_${Date.now()}_${Math.random().toString(36).substr(2, 5)}.bin`);
    
    try {
      fs.writeFileSync(tempBin, dataBuffer);
    } catch (err) {
      return reject(new Error(`Failed to write temporary receipt data file: ${err.message}`));
    }

    const tempBinEsc = tempBin.replace(/\\/g, '\\\\');
    const printerNameEsc = printerName.replace(/"/g, '`"');

    const psScript = `
Add-Type -TypeDefinition @'
using System;
using System.Runtime.InteropServices;
public class RawPrinter {
    [StructLayout(LayoutKind.Sequential, CharSet = CharSet.Ansi)]
    public struct DOC_INFO_1 {
        public string pDocName;
        public string pOutputFile;
        public string pDatatype;
    }
    [DllImport("winspool.drv", EntryPoint="OpenPrinterA", SetLastError=true)]
    public static extern bool OpenPrinter([MarshalAs(UnmanagedType.LPStr)] string name, ref IntPtr hPrinter, IntPtr pd);
    [DllImport("winspool.drv", SetLastError=true)]
    public static extern bool ClosePrinter(IntPtr hPrinter);
    [DllImport("winspool.drv", EntryPoint="StartDocPrinterA", SetLastError=true)]
    public static extern int StartDocPrinter(IntPtr hPrinter, int level, ref DOC_INFO_1 di);
    [DllImport("winspool.drv", SetLastError=true)]
    public static extern bool EndDocPrinter(IntPtr hPrinter);
    [DllImport("winspool.drv", SetLastError=true)]
    public static extern bool StartPagePrinter(IntPtr hPrinter);
    [DllImport("winspool.drv", SetLastError=true)]
    public static extern bool EndPagePrinter(IntPtr hPrinter);
    [DllImport("winspool.drv", SetLastError=true)]
    public static extern bool WritePrinter(IntPtr hPrinter, IntPtr pBytes, int cnt, ref int written);
    public static bool Send(string printerName, byte[] bytes) {
        IntPtr h = IntPtr.Zero;
        if (!OpenPrinter(printerName, ref h, IntPtr.Zero)) { return false; }
        DOC_INFO_1 di = new DOC_INFO_1 { pDocName="POS Receipt", pOutputFile=null, pDatatype="RAW" };
        if (StartDocPrinter(h, 1, ref di) == 0) { ClosePrinter(h); return false; }
        StartPagePrinter(h);
        IntPtr p = Marshal.AllocHGlobal(bytes.Length);
        Marshal.Copy(bytes, 0, p, bytes.Length);
        int w = 0;
        bool ok = WritePrinter(h, p, bytes.Length, ref w);
        Marshal.FreeHGlobal(p);
        EndPagePrinter(h); EndDocPrinter(h); ClosePrinter(h);
        return ok;
    }
}
'@
$bytes = [System.IO.File]::ReadAllBytes("${tempBinEsc}")
$ok = [RawPrinter]::Send("${printerNameEsc}", $bytes)
if ($ok) { Write-Host "PRINT_OK" } else { Write-Host "PRINT_FAILED" }
`;

    const psFile = path.join(tempDir, `rawprint_${Date.now()}.ps1`);
    try {
      fs.writeFileSync(psFile, psScript, 'utf8');
    } catch (err) {
      try { fs.unlinkSync(tempBin); } catch (_) {}
      return reject(new Error(`Failed to write PowerShell script: ${err.message}`));
    }

    // Purge stale browser GDI prints on this print queue first to prevent jamming
    const safePrinterNameForPurge = printerNameEsc.replace(/'/g, "''");
    const purgeCmd = `powershell -Command "Get-PrintJob -PrinterName '${safePrinterNameForPurge}' -ErrorAction SilentlyContinue | Remove-PrintJob -ErrorAction SilentlyContinue"`;
    
    exec(purgeCmd, () => {
      const printCmd = `powershell -ExecutionPolicy Bypass -File "${psFile}"`;
      
      exec(printCmd, (error, stdout, stderr) => {
        // Clean up temporary files
        try { fs.unlinkSync(tempBin); } catch (_) {}
        try { fs.unlinkSync(psFile); } catch (_) {}

        if (error) {
          return reject(error);
        }

        const log = stdout.toString() + stderr.toString();
        if (log.includes('PRINT_OK')) {
          resolve();
        } else {
          reject(new Error(`Spooler rejected the raw print job. Details: ${log}`));
        }
      });
    });
  });
}

// IPC print raw endpoint handler
ipcMain.handle('print-raw', async (event, printer, base64Data) => {
  try {
    const dataBuffer = Buffer.from(base64Data, 'base64');
    const connectionType = printer.connection_type || 'usb';

    if (connectionType === 'network') {
      const ip = printer.ip_address || '';
      const port = parseInt(printer.port || '9100', 10);
      if (!ip) {
        return { success: false, error: 'Network printer IP is not configured.' };
      }
      await sendToNetworkPrinter(ip, port, dataBuffer);
      return { success: true };
    } else {
      const printerName = printer.queue_name || printer.alias || '';
      if (!printerName) {
        return { success: false, error: 'Printer queue name or alias is not configured.' };
      }
      await sendToSpoolerPrinter(printerName, dataBuffer);
      return { success: true };
    }
  } catch (err) {
    console.error('IPC print-raw failure:', err);
    return { success: false, error: err.message };
  }
});

app.whenReady().then(() => {
  createWindow();

  app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) createWindow();
  });
});

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') app.quit();
});
