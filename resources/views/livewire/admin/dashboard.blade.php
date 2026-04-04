<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-2">Total Users</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ \App\Models\User::count() }}</p>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-2">Admin Users</h3>
                        <p class="text-3xl font-bold text-green-600">{{ \App\Models\User::role('Admin')->count() }}</p>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-2">Staff Users</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ \App\Models\User::role('Staff')->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    {{ __("Welcome to the Admin Dashboard, ") }} {{ Auth::user()->name }}!
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('users') }}" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <h4 class="font-medium">Manage Users</h4>
                            <p class="text-sm text-gray-600">View and assign user roles</p>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <h4 class="font-medium">Edit Profile</h4>
                            <p class="text-sm text-gray-600">Update your account settings</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>