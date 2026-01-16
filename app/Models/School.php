<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = ['latitude', 'longitude', 'radius'];
    public $timestamps = false;
}
