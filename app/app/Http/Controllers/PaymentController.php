<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    const AUTHORIZED = "Autorizado";
    const SENT = "Enviado";

    public function makePayment(Request $request){
        try {
            $data = $request->all();

            $value = $data['value'];
            if (empty($value)){
                throw new \Exception ('Invalid value', 400);
            }

            $payer = User::find($data['payer']);
            if (empty($payer)) {
                throw new \Exception ('Payer not found', 404);
            }

            $payee = User::find($data['payee']);
            if (empty($payee)) {
                throw new \Exception ('Payee not found', 404);
            }

            $paymentService = new PaymentService();
            $payment = $paymentService->createPayment($payer['id'], $payee['id'], $value);

            $paymentService->executePayment($payment);
            
            $paymentService->paymentDone($payment);

            return response()->json($payment, 200);
        } catch(\Exception $e) { 
            //Validate Http code
            $paymentService->paymentFail($payment);
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }
}