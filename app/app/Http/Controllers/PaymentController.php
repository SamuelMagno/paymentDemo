<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function makePayment(Request $request){

        $data = $request->all();

        $payer = User::find($data['payer']);

        if ($payer['isShopkeeper']){
            return response()->json(["message"=>"Shopkeepers cannot make transfers"], 400)
                ->header('Content-Type', 'application/json');
        }

        $payee = User::findOrFail($data['payee']);
        $value = $data['value'];

        $paymentData = [
            "payer" => $payer['id'],
            "payee" => $payee['id'],
            "value" => $value,
            "status" => "New",
        ];
        $payment = new Payment($paymentData);

        $payment->save();
        $payment->fresh();
        
        if($payer['wallet'] < $value) {

            $paymentData['status'] = "Failed";
            $payment->update($paymentData);

            return response()->json(["message"=>$paymentData], 400)
                ->header('Content-Type', 'application/json');
        }
        
        $payer['wallet'] -= $value;
        $payer->update();

        $payee['wallet'] += $value;
        $payee->update();

        $paymentData['status'] = "Done";
        $payment->update($paymentData);

        return response()->json($payment, 200)->header('Content-Type', 'application/json');
    }
}