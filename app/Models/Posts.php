<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
 {
    use HasFactory;

    protected $fillable = [
        'file_type',
        'file_id',
        'file_caption',
        'file_size',
        'file_uploader',
        'file_views',
        'file_downloads',
        'file_parent_path',
        'file_description',
        'file_thumbnails',
        'file_download_link',
        'file_remote_id'
    ];

}
