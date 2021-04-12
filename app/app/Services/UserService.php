<?php

namespace App\Services;

use App\Models\User;

class UserService 
{
    public function updateUser($user){
        try{
            $updatedUser = User::find($user['id']);
            $userInfo = [
                "name" => $user['name'],
                "tax_identification" => $user['tax_identification'],
                "email" => $user['email'],
                "wallet" => $user['wallet'],
                "isShopkeeper" => $user['isShopkeeper'],
            ];
            $updatedUser->update($userInfo);
        } catch(\Exception $e) {
            throw new \Exception ('Cannot update user', 500);
        }
    }

    public function addWallet($user, $value){
        try{
            $userAdded = User::find($user['id']);
            $userAdded['wallet'] += $value;
    
            return $userAdded;
        } catch(\Exception $e) {
            throw new \Exception ('Cannot add to wallet', 500);
        }

    }

    public function subtractWallet($user, $value){
        try{
            $userSubtracted = User::find($user['id']);
            $userSubtracted['wallet'] -= $value;
    
            return $userSubtracted;
        } catch(\Exception $e) {
            throw new \Exception ('Cannot subtract from wallet', 500);
        }
    }
}