<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['transporter_id', 'name', 'address', 'email'];
    
    public function transporters()
    {
        return $this->belongsTo(User::class, 'transporter_id');
    }
}
