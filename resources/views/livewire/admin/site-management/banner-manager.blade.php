<div class="max-w-6xl mx-auto">
    @if(session('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('message') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Banner Management</h1>
            <p class="text-gray-500 text-sm mt-1">Manage banners, promo strips and scheduled announcements.</p>
        </div>
        <button wire:click="openModal"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2.5 rounded-lg shadow flex items-center gap-2">
            <i class="fas fa-plus"></i> Add Banner
        </button>
    </div>

    <!-- Banners Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Preview</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Position</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Schedule</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @if(isset($banners) && $banners->count() > 0)
                    @foreach($banners as $banner)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="w-20 h-12 rounded-lg flex items-center justify-center text-white text-xs font-semibold"
                                 style="background: linear-gradient(to right, {{ $banner->bg_color }}, {{ $banner->bg_color }}cc)">
                                @if($banner->image_path)
                                    <img src="{{ Storage::url($banner->image_path) }}" class="w-20 h-12 object-cover rounded-lg">
                                @else
                                    {{ Str::limit($banner->title, 12) }}
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $banner->title }}</div>
                            @if($banner->subtitle)
                            <div class="text-xs text-gray-400">{{ Str::limit($banner->subtitle, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-xs font-semibold">
                                {{ isset($positions) && isset($positions[$banner->position]) ? $positions[$banner->position] : $banner->position }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            @if($banner->starts_at || $banner->ends_at)
                                @if($banner->starts_at)
                                    <div><i class="fas fa-play text-green-500 mr-1"></i>{{ $banner->starts_at->format('M d, Y H:i') }}</div>
                                @endif
                                @if($banner->ends_at)
                                    <div><i class="fas fa-stop text-red-500 mr-1"></i>{{ $banner->ends_at->format('M d, Y H:i') }}</div>
                                @endif
                            @else
                                <span class="text-gray-400">Always</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <button wire:click="toggleActive({{ $banner->id }})"
                                    class="px-2 py-1 rounded text-xs font-semibold {{ $banner->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $banner->is_active ? 'Active' : 'Inactive' }}
                            </button>
                            @if(!$banner->isLive() && $banner->is_active)
                            <div class="text-xs text-yellow-600 mt-0.5"><i class="fas fa-clock mr-1"></i>Scheduled</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 flex items-center gap-2">
                            <button wire:click="edit({{ $banner->id }})" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button wire:click="delete({{ $banner->id }})"
                                    onclick="confirm('Delete this banner?') || event.stopImmediatePropagation()"
                                    class="text-red-500 hover:text-red-700 text-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-images text-4xl mb-3 block"></i>
                            No banners yet. Click "Add Banner" to get started.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl z-50 relative">
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-bold">{{ $banner_id ? 'Edit Banner' : 'New Banner' }}</h3>
                    <button wire:click="$set('isOpen', false)" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="store">
                    <div class="p-6 max-h-[70vh] overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                            <input type="text" wire:model="title"
                                   class="w-full border-gray-300 rounded-lg shadow-sm">
                            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                            <input type="text" wire:model="subtitle" class="w-full border-gray-300 rounded-lg shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Position *</label>
                            <select wire:model="position" class="w-full border-gray-300 rounded-lg shadow-sm">
                                @if(isset($positions) && is_array($positions))
                                    @foreach($positions as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Caption / Body Text</label>
                            <textarea wire:model="caption" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
                            <input type="text" wire:model="button_text" class="w-full border-gray-300 rounded-lg shadow-sm" placeholder="Shop Now">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Button Link</label>
                            <input type="text" wire:model="button_link" class="w-full border-gray-300 rounded-lg shadow-sm" placeholder="/products">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Background Color</label>
                            <div class="flex gap-2">
                                <input type="color" wire:model="bg_color" class="w-12 h-10 rounded border p-0.5 cursor-pointer">
                                <input type="text" wire:model="bg_color" class="flex-1 border-gray-300 rounded-lg text-sm font-mono">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Text Color</label>
                            <div class="flex gap-2">
                                <input type="color" wire:model="text_color" class="w-12 h-10 rounded border p-0.5 cursor-pointer">
                                <input type="text" wire:model="text_color" class="flex-1 border-gray-300 rounded-lg text-sm font-mono">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Starts At (optional)</label>
                            <input type="datetime-local" wire:model="starts_at" class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ends At (optional)</label>
                            <input type="datetime-local" wire:model="ends_at" class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                            @error('ends_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Banner Image</label>
                            @if($image_path)
                                <img src="{{ Storage::url($image_path) }}" class="h-24 mb-2 rounded-lg object-cover w-full">
                            @endif
                            <input type="file" wire:model="image" accept="image/*" class="w-full text-sm">
                        </div>

                        <div class="col-span-2">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                <input type="checkbox" wire:model="is_active" class="rounded"> Active
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                            <input type="number" wire:model="sort_order" min="0" class="w-full border-gray-300 rounded-lg shadow-sm">
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t flex justify-end gap-2">
                        <button type="button" wire:click="$set('isOpen', false)"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                        <button type="submit"
                                class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                            {{ $banner_id ? 'Update Banner' : 'Create Banner' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>