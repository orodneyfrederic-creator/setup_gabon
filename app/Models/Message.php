<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'contenu',
        'date_envoi',
        'lu',
        'id_conv',
        'id_ut_expediteur',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'id_conv');
    }

    public function expediteur()
    {
        return $this->belongsTo(User::class, 'id_ut_expediteur');
    }
}
