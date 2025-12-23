<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Van extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'license_plate',
        'capacity',
        'status',
        'description',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getAvailableSeats($date)
    {
        $bookedSeats = $this->bookings()
            ->where('travel_date', $date)
            ->where('status', 'approved')
            ->sum('seats_requested');
        
        return $this->capacity - $bookedSeats;
    }

    public function isAvailable($date)
    {
        return $this->status === 'active' && $this->getAvailableSeats($date) > 0;
    }
}
