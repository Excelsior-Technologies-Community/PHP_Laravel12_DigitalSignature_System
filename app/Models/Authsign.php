<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authsign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'password', 'signature'
    ];

    protected $hidden = [
        'password'
    ];
}
