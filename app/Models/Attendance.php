<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'date',
        'time',
        'latitude',
        'longitude',
        'distance',
        'status',
        'note'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
