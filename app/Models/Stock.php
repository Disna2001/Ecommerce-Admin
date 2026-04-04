<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
        'sku',
        'item_code',
        'name',
        'description',
        'category_id',
        'make_id',
        'brand_id',
        'item_type_id',
        'supplier_id',
        'warranty_id',
        'quantity',
        'reorder_level',
        'unit_price',
        'selling_price',
        'wholesale_price',
        'location',
        'barcode',
        'status',
        'model_name',
        'model_number',
        'color',
        'size',
        'weight',
        'specifications',
        'images',
        'videos',
        'tags',
        'notes',
        'quality_level',
        'target_category_id',
        'target_item_type_id',
        'target_make_id',
        'target_brand_id',
        'target_model',
        'target_model_number',
    ];

    protected $casts = [
        'specifications' => 'array',
        'images'         => 'array',
        'videos'         => 'array',
        'unit_price'     => 'decimal:2',
        'selling_price'  => 'decimal:2',
        'wholesale_price'=> 'decimal:2',
        'weight'         => 'decimal:2',
        'quantity'       => 'integer',
        'reorder_level'  => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function make()
    {
        return $this->belongsTo(Make::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function itemType()
    {
        return $this->belongsTo(ItemType::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }

    public function qualityLevel()
    {
        return $this->belongsTo(ItemQualityLevel::class, 'quality_level', 'code');
    }

    public function targetCategory()
    {
        return $this->belongsTo(Category::class, 'target_category_id');
    }

    public function targetMake()
    {
        return $this->belongsTo(Make::class, 'target_make_id');
    }

    public function targetBrand()
    {
        return $this->belongsTo(Brand::class, 'target_brand_id');
    }

    public function targetItemType()
    {
        return $this->belongsTo(ItemType::class, 'target_item_type_id');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->reorder_level;
    }
}
