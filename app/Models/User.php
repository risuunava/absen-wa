<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'phone',
        'password',
        'role',
        'full_name',
        'class',
        'subject'
    ];

    protected $hidden = [
        'password'
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function isMurid()
    {
        return $this->role === 'murid';
    }

    public function isGuru()
    {
        return $this->role === 'guru';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}