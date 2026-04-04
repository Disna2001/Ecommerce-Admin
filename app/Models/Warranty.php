<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = ['tenant_id', 'name', 'type', 'duration', 'terms', 'coverage', 'status'];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function getDurationTextAttribute()
    {
        if ($this->duration < 12) {
            return $this->duration . ' month' . ($this->duration > 1 ? 's' : '');
        }
        
        $years = floor($this->duration / 12);
        $months = $this->duration % 12;
        
        $text = $years . ' year' . ($years > 1 ? 's' : '');
        if ($months > 0) {
            $text .= ' ' . $months . ' month' . ($months > 1 ? 's' : '');
        }
        
        return $text;
    }
}
