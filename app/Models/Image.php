<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Image extends Model
{
    protected $collection = 'images';

    protected $fillable = [
        'base64_data',
        'mime_type'
    ];
}
