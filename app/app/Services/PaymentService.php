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
    
    private UserService $userService;
    private Authorization $authorizationRule;
    private NotificationService $notificationService;


    public function __construct(UserService $userService, Authorization $authorizationRule, NotificationService $notificationService) {
        $this->userService = $userService;
        $this->authorizationRule = $authorizationRule;
        $this->notificationService = $notificationService;
    }
    public function createPayment(int $payerId, int $payeeId, float $value) : Payment {
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

    public function executePayment(Payment $payment) : void {
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
            
            if (!$this->authorizationRule->isPaymentAuthorized($payment)) {
                throw new \Exception ('Unauthorized payment', 400);
            }

            DB::beginTransaction();

            $payer = $this->userService->subtractWallet($payer, $value);
            $this->userService->updateUser($payer);

            $payee = $this->userService->addWallet($payee, $value);
            $this->userService->updateUser($payee);

            DB::commit();
            
            $this->notificationService->sendNotification($payment);

        } catch(\Exception $e){
            DB::rollBack();
            throw new \Exception ($e->getMessage(), 400);
        }
    }

    public function paymentFail(Payment $payment) : void {
        try{
            $paymentInfo = [
                "payer" => $payment['payer'],
                "payee" => $payment['payee'],
                "value" => $payment['value'],
                "status" => self::FAILED,
            ];
            $payment->update($paymentInfo);
        } catch(\Exception $e) {
            throw new \Exception ('Cannot update payment', 500);
        }
    }

    public function paymentDone(Payment $payment) : void {
        try{
            $paymentInfo = [
                "payer" => $payment['payer'],
                "payee" => $payment['payee'],
                "value" => $payment['value'],
                "status" => self::DONE,
            ];
            $payment->update($paymentInfo);
        } catch(\Exception $e) {
            throw new \Exception ('Cannot update payment', 500);
        }
    }
}