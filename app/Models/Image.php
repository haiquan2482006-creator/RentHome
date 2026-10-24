<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Image extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'images';

    protected $fillable = [
        'target_type',
        'target_id',
        'filename',
        'mime_type',
        'size',
        'data',
        'base64_data', // Duy trì tương thích ngược
        'is_cover',
        'uploaded_by',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
        'size' => 'integer',
    ];

    /**
     * Lấy URL trực tiếp để hiển thị ảnh
     */
    public function getUrlAttribute()
    {
        return url('/images/' . $this->_id);
    }
}
