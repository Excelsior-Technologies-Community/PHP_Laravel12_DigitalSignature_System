<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['authsign_id', 'action', 'ip_address', 'user_agent', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

   public function user()
{
    return $this->belongsTo(Authsign::class, 'authsign_id');
}
}
