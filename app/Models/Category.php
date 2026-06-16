<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($c) => $c->slug ??= Str::slug($c->name));
        static::updating(fn($c) => $c->slug = Str::slug($c->name));
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}