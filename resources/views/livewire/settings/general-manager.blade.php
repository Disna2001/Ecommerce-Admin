<div>
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="saveGeneral">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Application Settings -->
            <div class="col-span-2">
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Application Settings</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Application Name</label>
                <input type="text" wire:model="appName" class="w-full rounded-md border-gray-300 shadow-sm">
                @error('appName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Application URL</label>
                <input type="url" wire:model="appUrl" class="w-full rounded-md border-gray-300 shadow-sm">
                @error('appUrl') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Environment</label>
                <select wire:model="appEnv" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="local">Local</option>
                    <option value="development">Development</option>
                    <option value="staging">Staging</option>
                    <option value="production">Production</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Debug Mode</label>
                <select wire:model="appDebug" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="1">Enabled</option>
                    <option value="0">Disabled</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                <select wire:model="timezone" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="UTC">UTC</option>
                    <option value="America/New_York">America/New York</option>
                    <option value="America/Chicago">America/Chicago</option>
                    <option value="America/Denver">America/Denver</option>
                    <option value="America/Los_Angeles">America/Los Angeles</option>
                    <option value="Asia/Dubai">Asia/Dubai</option>
                    <option value="Asia/Kolkata">Asia/Kolkata</option>
                    <option value="Asia/Singapore">Asia/Singapore</option>
                </select>
                @error('timezone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                <select wire:model="currency" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="USD">USD - US Dollar</option>
                    <option value="EUR">EUR - Euro</option>
                    <option value="GBP">GBP - British Pound</option>
                    <option value="JPY">JPY - Japanese Yen</option>
                    <option value="AED">AED - UAE Dirham</option>
                    <option value="INR">INR - Indian Rupee</option>
                    <option value="SGD">SGD - Singapore Dollar</option>
                </select>
                @error('currency') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                <select wire:model="language" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="en">English</option>
                    <option value="es">Spanish</option>
                    <option value="fr">French</option>
                    <option value="de">German</option>
                    <option value="ar">Arabic</option>
                    <option value="hi">Hindi</option>
                    <option value="zh">Chinese</option>
                </select>
            </div>

            <!-- Company Information -->
            <div class="col-span-2 mt-6">
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Company Information</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                <input type="text" wire:model="companyName" class="w-full rounded-md border-gray-300 shadow-sm">
                @error('companyName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Company Email</label>
                <input type="email" wire:model="companyEmail" class="w-full rounded-md border-gray-300 shadow-sm">
                @error('companyEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Company Phone</label>
                <input type="text" wire:model="companyPhone" class="w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Company Address</label>
                <textarea wire:model="companyAddress" rows="2" class="w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>

            <!-- Display Settings -->
            <div class="col-span-2 mt-6">
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Display Settings</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Format</label>
                <select wire:model="dateFormat" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="Y-m-d">2024-12-31</option>
                    <option value="m/d/Y">12/31/2024</option>
                    <option value="d/m/Y">31/12/2024</option>
                    <option value="d M Y">31 Dec 2024</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Time Format</label>
                <select wire:model="timeFormat" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="H:i">14:30 (24 hour)</option>
                    <option value="h:i A">02:30 PM (12 hour)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Items Per Page</label>
                <input type="number" wire:model="itemsPerPage" class="w-full rounded-md border-gray-300 shadow-sm">
                @error('itemsPerPage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Theme</label>
                <select wire:model="theme" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="light">Light</option>
                    <option value="dark">Dark</option>
                    <option value="auto">Auto (System)</option>
                </select>
            </div>

            <!-- Inventory Settings -->
            <div class="col-span-2 mt-6">
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Inventory Settings</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Low Stock Threshold</label>
                <input type="number" wire:model="lowStockThreshold" class="w-full rounded-md border-gray-300 shadow-sm">
                @error('lowStockThreshold') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Enable Notifications</label>
                <select wire:model="enableNotifications" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <!-- Maintenance & Backup -->
            <div class="col-span-2 mt-6">
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Maintenance & Backup</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Maintenance Mode</label>
                <div class="flex items-center">
                    <span class="mr-3 {{ $maintenanceMode ? 'text-red-600' : 'text-green-600' }}">
                        {{ $maintenanceMode ? 'Maintenance Mode Active' : 'Site is Live' }}
                    </span>
                    <button type="button" wire:click="toggleMaintenance" 
                            class="px-4 py-2 {{ $maintenanceMode ? 'bg-green-500 hover:bg-green-600' : 'bg-yellow-500 hover:bg-yellow-600' }} text-white rounded">
                        {{ $maintenanceMode ? 'Bring Site Live' : 'Enable Maintenance' }}
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Auto Backup</label>
                <select wire:model="autoBackup" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="1">Enabled</option>
                    <option value="0">Disabled</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Backup Frequency</label>
                <select wire:model="backupFrequency" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="hourly">Hourly</option>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="button" wire:click="runBackup" 
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Run Backup Now
                </button>
            </div>

            <!-- System Tools -->
            <div class="col-span-2 mt-6">
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">System Tools</h3>
            </div>

            <div class="col-span-2">
                <button type="button" wire:click="clearCache" 
                        class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
                    Clear All Caches
                </button>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Save Settings
            </button>
        </div>
    </form>
</div>