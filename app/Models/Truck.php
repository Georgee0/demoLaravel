<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    protected $fillable = ['transporter_id', 'plate_number', 'model', 'color'];

    // protected function setPlateNumberAttribute($value)
    // {
    //     if (is_null($value)) {
    //         $this->attributes['plate_number'] = $value;
    //         return;
    //     }

    //     $val = strtoupper(trim($value));
    //     if (strpos($val, 'TRK-') !== 0) {
    //         $val = 'TRK-' . $val;
    //     }

    //     $this->attributes['plate_number'] = $val;
    // }

    public function transporters()
    {
        return $this->belongsTo(User::class, 'transporter_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}