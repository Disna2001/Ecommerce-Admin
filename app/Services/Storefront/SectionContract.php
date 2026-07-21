<?php

namespace App\Services\Storefront;

interface SectionContract
{
    public function key(): string;

    public function label(): string;

    public function bladeView(): string;

    public function schema(): array;

    public function defaults(): array;
}
