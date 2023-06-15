<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotUsers extends Model
 {
    use HasFactory;

    protected $fillable = [
        'username',
        'first_name',
        'chat_id',
        'chat_id',
        'last_name',
        'last_checkin',
        'search_history',
        'watch_history',
        'saved_items',
        'user_type',
        'query_count',
        'role'
    ];

    protected $casts = [
        'search_history'=>'array',
        'watch_history'=>'array',
        'saved_items'=>'array',
        'role'=>'array'
    ];
}
