<?php

namespace App\Rules;

use App\Models\User;

class Payer
{

    public function isShopkeeper(User $payer) : bool {
        try {
           
            return $payer['isShopkeeper'];
            
        } catch (\Exception $e) {
            throw new \Exception ('Cannot validate payer', 500);
        }
    }

    public function isFundsSufficient(User $payer, float $value) : bool {
        try {
           
            return $payer['wallet'] > $value;
            
        } catch (\Exception $e) {
            throw new \Exception ('Cannot validate funds', 500);
        }
    }
}