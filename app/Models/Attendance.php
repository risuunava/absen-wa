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
        'verified',
        'photo_verified',
        'attendance_setting_id',
        'attendance_type'
    ];

    protected $casts = [
        'date' => 'date',
        'distance' => 'decimal:2',
        'verified' => 'boolean',
        'photo_verified' => 'boolean'
    ];

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = substr($value, 0, 20);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceSetting()
    {
        return $this->belongsTo(AttendanceTimeSetting::class, 'attendance_setting_id');
    }
    
    public function getSelfiePhotoUrlAttribute()
    {
        if ($this->selfie_photo) {
            return asset('storage/' . $this->selfie_photo);
        }
        return null;
    }
}