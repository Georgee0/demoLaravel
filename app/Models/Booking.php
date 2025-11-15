<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['transporter_id', 'truck_id', 'driver_id', 'booking_date', 'terminal', 'status'];

     // default attribute when status is not provided at all
    protected $attributes = [
        'status' => 'pending',
    ];

    // cast booking_date to a date object
    protected $casts = [
        'booking_date' => 'date',
    ];
    
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                // generate a unique booking code like BKG-XXXXXXXX
                do {
                    $code = 'BKG-' . strtoupper(\Illuminate\Support\Str::random(8));
                } while (self::where('booking_code', $code)->exists());

                $booking->booking_code = $code;
            }
        });
    }
    
    public function transporter()
    {
        return $this->belongsTo(User::class, 'transporter_id');
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}