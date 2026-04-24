<?php

namespace App\Livewire\Admin\SiteManagement;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Banner;

class BannerManager extends Component
{
    use WithFileUploads;

    public $banner_id;
    public $title        = '';
    public $subtitle     = '';
    public $caption      = '';
    public $button_text  = '';
    public $button_link  = '';
    public $image;
    public $image_path   = '';
    public $position     = 'hero';
    public $bg_color     = '#4f46e5';
    public $text_color   = '#ffffff';
    public $is_active    = true;
    public $sort_order   = 0;
    public $starts_at    = '';
    public $ends_at      = '';
    public $isOpen       = false;

    public $positions = [
        'hero'    => 'Hero (Main)',
        'promo'   => 'Promo Strip',
        'sidebar' => 'Sidebar',
        'top_bar' => 'Top Bar',
    ];

    protected function rules()
    {
        return [
            'title'       => 'required|string|max:200',
            'subtitle'    => 'nullable|string|max:300',
            'caption'     => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string|max:500',
            'position'    => 'required|in:hero,promo,sidebar,top_bar',
            'bg_color'    => 'required|string',
            'text_color'  => 'required|string',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer|min:0',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date|after_or_equal:starts_at',
            'image'       => 'nullable|image|max:2048',
        ];
    }

    public function openModal()
    {
        $this->reset(['banner_id','title','subtitle','caption','button_text','button_link',
                      'image','image_path','position','bg_color','text_color','is_active',
                      'sort_order','starts_at','ends_at']);
        $this->is_active  = true;
        $this->bg_color   = '#4f46e5';
        $this->text_color = '#ffffff';
        $this->position   = 'hero';
        $this->isOpen     = true;
        $this->resetValidation();
    }

    public function applyPreset(string $preset): void
    {
        if (!$this->isOpen) {
            $this->openModal();
        }

        if ($preset === 'hero_launch') {
            $this->title = 'Launch a bold hero campaign';
            $this->subtitle = 'Hero spotlight';
            $this->caption = 'Use this for seasonal launches, top-selling collections, or premium storefront announcements.';
            $this->button_text = 'Shop collection';
            $this->button_link = '/products';
            $this->position = 'hero';
            $this->bg_color = '#312e81';
            $this->text_color = '#ffffff';
        }

        if ($preset === 'promo_strip') {
            $this->title = 'Weekend deal now live';
            $this->subtitle = 'Promo strip';
            $this->caption = 'Keep a shorter message here for bundle offers, free shipping, or limited-time coupon pushes.';
            $this->button_text = 'View deal';
            $this->button_link = '/products';
            $this->position = 'promo';
            $this->bg_color = '#0f766e';
            $this->text_color = '#ffffff';
        }

        if ($preset === 'top_notice') {
            $this->title = 'Free delivery on selected orders';
            $this->subtitle = 'Top notice';
            $this->caption = 'A compact line for service updates, delivery promises, or trust-building announcements.';
            $this->button_text = 'Learn more';
            $this->button_link = '/help-center';
            $this->position = 'top_bar';
            $this->bg_color = '#7c2d12';
            $this->text_color = '#ffffff';
        }
    }

    public function edit($id)
    {
        $b = Banner::findOrFail($id);
        $this->banner_id  = $id;
        $this->title      = $b->title;
        $this->subtitle   = $b->subtitle;
        $this->caption    = $b->caption;
        $this->button_text = $b->button_text;
        $this->button_link = $b->button_link;
        $this->image_path  = $b->image_path;
        $this->position   = $b->position;
        $this->bg_color   = $b->bg_color;
        $this->text_color = $b->text_color;
        $this->is_active  = $b->is_active;
        $this->sort_order = $b->sort_order;
        $this->starts_at  = $b->starts_at?->format('Y-m-d\TH:i');
        $this->ends_at    = $b->ends_at?->format('Y-m-d\TH:i');
        $this->isOpen     = true;
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        $data = [
            'title'      => $this->title,
            'subtitle'   => $this->subtitle,
            'caption'    => $this->caption,
            'button_text'=> $this->button_text,
            'button_link'=> $this->button_link,
            'position'   => $this->position,
            'bg_color'   => $this->bg_color,
            'text_color' => $this->text_color,
            'is_active'  => $this->is_active,
            'sort_order' => $this->sort_order,
            'starts_at'  => $this->starts_at ?: null,
            'ends_at'    => $this->ends_at   ?: null,
        ];

        if ($this->image) {
            $data['image_path'] = $this->image->store('banners', 'public');
        }

        Banner::updateOrCreate(['id' => $this->banner_id ?: null], $data);

        session()->flash('message', $this->banner_id ? 'Banner updated.' : 'Banner created.');
        $this->isOpen = false;
    }

    public function toggleActive($id)
    {
        $b = Banner::findOrFail($id);
        $b->update(['is_active' => !$b->is_active]);
    }

    public function moveUp($id): void
    {
        $banner = Banner::findOrFail($id);
        $previous = Banner::query()
            ->where('position', $banner->position)
            ->where('sort_order', '<', $banner->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if (!$previous) {
            return;
        }

        $currentOrder = (int) $banner->sort_order;
        $banner->update(['sort_order' => $previous->sort_order]);
        $previous->update(['sort_order' => $currentOrder]);
    }

    public function moveDown($id): void
    {
        $banner = Banner::findOrFail($id);
        $next = Banner::query()
            ->where('position', $banner->position)
            ->where('sort_order', '>', $banner->sort_order)
            ->orderBy('sort_order')
            ->first();

        if (!$next) {
            return;
        }

        $currentOrder = (int) $banner->sort_order;
        $banner->update(['sort_order' => $next->sort_order]);
        $next->update(['sort_order' => $currentOrder]);
    }

    public function delete($id)
    {
        Banner::findOrFail($id)->delete();
        session()->flash('message', 'Banner deleted.');
    }

    public function render()
    {
        $banners = Banner::orderBy('position')->orderBy('sort_order')->orderByDesc('created_at')->get();

        return view('livewire.admin.site-management.banner-manager', [
            'banners'   => $banners,
            'positions' => $this->positions,
            'bannerStats' => [
                'total' => $banners->count(),
                'live' => $banners->filter(fn (Banner $banner) => $banner->isLive())->count(),
                'scheduled' => $banners->filter(fn (Banner $banner) => $banner->is_active && !$banner->isLive())->count(),
                'hero' => $banners->where('position', 'hero')->count(),
            ],
        ]);
    }
}
