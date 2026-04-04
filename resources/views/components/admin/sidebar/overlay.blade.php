<div x-show="sidebarOpen && isMobile" @click="closeSidebar()" 
     class="mobile-overlay"
     :class="{ 'active': sidebarOpen && isMobile }"
     x-cloak
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
</div>
