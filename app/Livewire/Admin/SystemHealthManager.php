<?php

namespace App\Livewire\Admin;

use App\Services\Operations\HostingReadinessService;
use Livewire\Component;

class SystemHealthManager extends Component
{
    public int $staleWindowMinutes = 15;

    public function render()
    {
        return view('livewire.admin.system-health-manager', app(HostingReadinessService::class)
            ->buildReport($this->staleWindowMinutes));
    }
}
