<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceTimeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'type',
        'is_active',
        'days_of_week',
        'description'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean'
    ];

    // Accessor untuk mendapatkan days sebagai array
    public function getDaysArrayAttribute()
    {
        if (empty($this->days_of_week)) {
            return [];
        }
        
        if (is_array($this->days_of_week)) {
            return $this->days_of_week;
        }
        
        $decoded = json_decode($this->days_of_week, true);
        return is_array($decoded) ? $decoded : [];
    }

    // Get days display
    public function getDaysDisplayAttribute()
    {
        $daysArray = $this->days_array;
        
        if (empty($daysArray)) {
            return 'Setiap Hari';
        }

        $dayNames = [
            1 => 'Senin',
            2 => 'Selasa', 
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];

        $days = array_map(function($day) use ($dayNames) {
            return $dayNames[$day] ?? $day;
        }, $daysArray);

        return implode(', ', $days);
    }

    // Cek apakah setting berlaku hari ini
    public function isActiveToday()
    {
        if (!$this->is_active) {
            return false;
        }
        
        $daysArray = $this->days_array;
        
        if (empty($daysArray)) {
            return true;
        }
        
        $currentDay = now()->dayOfWeekIso;
        return in_array($currentDay, $daysArray);
    }

    // Format waktu untuk display
    public function getTimeRangeAttribute()
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    // Scope untuk setting aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}