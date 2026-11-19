<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadSetting extends Model
{
    protected $fillable = [
        'max_file_size_mb',
        'accepted_formats',
    ];

    protected function casts(): array
    {
        return [
            'max_file_size_mb' => 'integer',
            'accepted_formats' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'max_file_size_mb' => 10,
                'accepted_formats' => ['pdf', 'png', 'jpg', 'jpeg'],
            ]
        );
    }
}
