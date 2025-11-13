<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = ['transporter_id', 'name', 'phone', 'email', 'driver_license'];

    // protected function setDriverLicenseAttribute($value)
    // {
    //     if (is_null($value)) {
    //         $this->attributes['driver_license'] = $value;
    //         return;
    //     }

    //     $val = strtoupper(trim($value));
    //     if (strpos($val, 'DRV-') !== 0) {
    //         $val = 'DRV-' . $val;
    //     }

    //     $this->attributes['driver_license'] = $val;
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