<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authsign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'password', 'signature', 'remember_token', 'email_verified_at', 'is_admin', 'last_login_at'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
