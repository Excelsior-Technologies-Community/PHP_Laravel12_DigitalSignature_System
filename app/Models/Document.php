<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'authsign_id', 'file_name', 'file_path', 'mime_type', 'file_size', 'status', 'signed_at'
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Authsign::class);
    }
}
