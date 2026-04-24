<div class="space-y-6 p-6 max-w-full">
    <div x-data="{ show:false, message:'', type:'success' }" x-on:notify.window="show=true; message=$event.detail.message; type=$event.detail.type; setTimeout(()=>show=false,3500)" x-show="show" x-transition class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-semibold text-white shadow-xl" :class="type==='success'?'bg-green-500':(type==='error'?'bg-red-500':'bg-indigo-500')" style="display:none">
        <i class="fas fa-check-circle"></i><span x-text="message"></span>
    </div>

    @include('components.admin.reviews.summary')
    @include('components.admin.reviews.filters')
    @include('components.admin.reviews.table')
    @include('components.admin.reviews.view-modal')
    @include('components.admin.reviews.edit-modal')
</div>
