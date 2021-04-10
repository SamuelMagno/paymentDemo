<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    public function paymentsPayer(){
        return $this->hasMany(Payment::class, 'payer');
    }
    
    public function paymentsPayee(){
        return $this->hasMany(Payment::class, 'payee');
    }
}
