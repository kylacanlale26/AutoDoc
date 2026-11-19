<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAnalysis extends Model
{
    protected $fillable = [
        'document_id',
        'status',
        'raw_text',
        'result',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'result' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
