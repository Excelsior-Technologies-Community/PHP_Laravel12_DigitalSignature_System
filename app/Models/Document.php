<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'authsign_id',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'status',
        'signed_at',
        'expires_at',
        'verification_code',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'expires_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(Authsign::class, 'authsign_id');
    }

    /**
     * Check whether the document has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null
            && $this->expires_at->isBefore(today());
    }

    /**
     * Check whether the signed document is currently valid.
     */
    public function isValid(): bool
    {
        return $this->status === 'signed'
            && !$this->isExpired();
    }
}