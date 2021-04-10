<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['payer', 'payee', 'value', 'status'];
    public function payer(){
        return $this->belongsTo(User::class, 'id', 'payer');
    }

    public function payee(){
        return $this->belongsTo(User::class, 'id', 'payer');
    }
}
