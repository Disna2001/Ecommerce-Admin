<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}

                    @can('view-admin-menu')
                        <div class="mt-4 rounded-lg bg-yellow-100 p-4">
                            <p class="text-yellow-800">
                                You have admin privileges.
                                <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:underline">Go to Admin Dashboard</a>
                            </p>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
