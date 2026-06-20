const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('api', {
  getConfig: () => ipcRenderer.invoke('get-config'),
  saveConfig: (config) => ipcRenderer.invoke('save-config', config),
  clearCache: () => ipcRenderer.invoke('clear-cache'),
  launchView: () => ipcRenderer.invoke('launch-view'),
  printRaw: (printer, base64Data) => ipcRenderer.invoke('print-raw', printer, base64Data),
  onConnectionError: (callback) => ipcRenderer.on('connection-error', (event, data) => callback(data)),
});
