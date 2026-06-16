<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AgentConversation extends Model
{
    public $incrementing = false; 

    protected $keyType = 'string'; 

    protected static function booted()
    {
        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $fillable = ['id', 'title', 'user_id'];
}