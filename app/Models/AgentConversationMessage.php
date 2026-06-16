<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AgentConversationMessage extends Model
{
    protected $table = 'agent_conversation_messages';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'conversation_id',
        'user_id',
        'agent',
        'role',
        'content',
        'attachments',
        'tool_calls',
        'tool_results',
        'usage',
        'meta',
    ];

    protected $casts = [
        'attachments' => 'array',
        'tool_calls'  => 'array',
        'tool_results'=> 'array',
        'usage'       => 'array',
        'meta'        => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function conversation()
    {
        return $this->belongsTo(AgentConversation::class, 'conversation_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}