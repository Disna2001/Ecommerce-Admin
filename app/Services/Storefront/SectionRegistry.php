<?php

namespace App\Services\Storefront;

use App\Services\Storefront\Sections\BannerRailSection;
use App\Services\Storefront\Sections\CategoriesSection;
use App\Services\Storefront\Sections\DealsSection;
use App\Services\Storefront\Sections\FeaturedProductsSection;
use App\Services\Storefront\Sections\FinalCtaSection;
use App\Services\Storefront\Sections\FooterSection;
use App\Services\Storefront\Sections\HeroSection;
use App\Services\Storefront\Sections\NewArrivalsSection;
use App\Services\Storefront\Sections\PromoStripSection;
use App\Services\Storefront\Sections\ReviewsSection;

class SectionRegistry
{
    protected array $sections = [];

    public function __construct()
    {
        $this->register(new HeroSection());
        $this->register(new BannerRailSection());
        $this->register(new FeaturedProductsSection());
        $this->register(new NewArrivalsSection());
        $this->register(new DealsSection());
        $this->register(new FooterSection());
        $this->register(new CategoriesSection());
        $this->register(new ReviewsSection());
        $this->register(new FinalCtaSection());
        $this->register(new PromoStripSection());
    }

    public function register(SectionContract $section): void
    {
        $this->sections[$section->key()] = $section;
    }

    public function all(): array
    {
        return $this->sections;
    }

    public function get(string $key): ?SectionContract
    {
        return $this->sections[$key] ?? null;
    }

    public function toRegistryArray(): array
    {
        $result = [];
        foreach ($this->sections as $key => $section) {
            $result[$key] = [
                'key' => $section->key(),
                'label' => $section->label(),
                'schema' => $section->schema(),
                'defaults' => $section->defaults(),
            ];
        }
        return $result;
    }
}
