<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'titre',
        'message',
        'lu',
        'date_creation',
        'id_ut',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_ut');
    }
}
