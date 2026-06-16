<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'price', 'stock', 'image', 'is_active',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($p) => $p->slug ??= Str::slug($p->name));
        static::updating(fn($p) => $p->slug = Str::slug($p->name));
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}