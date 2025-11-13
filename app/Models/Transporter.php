<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transporter extends Model
{
    protected $fillable = ['company_id', 'name', 'phone', 'email'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }
    public function trucks()
    {
        return $this->hasMany(Truck::class);
    }    
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }    
}