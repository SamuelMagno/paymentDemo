<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Payment;
use App\Rules\Authorization;
use App\Rules\Payer;
use App\Services\NotificationService;
use App\Services\UserService;

class PaymentService 
{
    const FAILED = "failed";
    const DONE = "done";
    const CREATED = "new";
    
    public function createPayment($payerId, $payeeId, $value)
    {
        try{
            $payment = Payment::create([
                "payer" => $payerId,
                "payee" => $payeeId,
                "value" => $value,
                "status" => self::CREATED,
            ]);
            return $payment;
        } catch(\Exception $e) {
            throw new \Exception ('Cannot create payment', 500);
        }
    }

    public function executePayment($payment)
    {
        try{
            $payer = User::find($payment['payer']);
            $payee = User::find($payment['payee']);
            $value = $payment['value'];

            $userRules = new Payer;
            if($userRules->isShopkeeper($payer)) {
                throw new \Exception ('Shopkeepers cannot make transfers', 400);
            }
        
            if(!$userRules->isFundsSufficient($payer, $value)) {
                throw new \Exception ('Insufficient Funds', 400);
            }
            
            $authorizationRule = new Authorization();
            if (!$authorizationRule->isPaymentAuthorized($payment)) {
                throw new \Exception ('Unauthorized payment', 400);
            }

            DB::beginTransaction();

            $userService = new UserService();
            
            $payer = $userService->subtractWallet($payer, $value);
            $userService->updateUser($payer);

            $payee = $userService->addWallet($payee, $value);
            $userService->updateUser($payee);

            DB::commit();
            
            $notificationService = new NotificationService();
            $notificationService->sendNotification($payment);

        } catch(\Exception $e){
            DB::rollBack();
            throw new \Exception ($e->getMessage(), 400);
        }
    }

    public function paymentFail($payment)
    {
        try{
            $updatedPayment = Payment::find($payment['id']);
            $updatedPayment['status'] = self::FAILED;
            $updatedPayment->update();
        } catch(\Exception $e) {
            throw new \Exception ('Cannot update payment', 500);
        }
    }

    public function paymentDone($payment)
    {
        try{
            $updatedPayment = Payment::find($payment['id']);
            $updatedPayment['status'] = self::DONE;
            $updatedPayment->update();
        } catch(\Exception $e) {
            throw new \Exception ('Cannot update payment', 500);
        }
    }
}