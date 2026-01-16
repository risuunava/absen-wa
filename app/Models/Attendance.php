<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
        'date',
        'time',
        'latitude',
        'longitude',
        'distance',
        'status',
        'note',
        'selfie_photo',
        'photo_verified'
    ];

    protected $casts = [
        'date' => 'date',
        'distance' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}