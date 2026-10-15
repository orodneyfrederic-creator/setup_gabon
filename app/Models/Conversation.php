<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'statut',
        'date_creation',
        'id_ut_client',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'id_ut_client');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'id_conv');
    }
}
