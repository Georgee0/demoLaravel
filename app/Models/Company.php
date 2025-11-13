<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'address', 'email'];
    
    public function transporters()
    {
        return $this->hasMany(User::class);
    }
}
