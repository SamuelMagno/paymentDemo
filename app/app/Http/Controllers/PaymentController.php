<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\User;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    const AUTHORIZED = "Autorizado";
    const SENT = "Enviado";

    public function makePayment(Request $request){
        
        $request->validate([
            'value' => ['required', 'numeric', 'min:0.01'],
            'payer' => ['required', 'integer', 'min:1'],
            "payee" => ['required', 'integer', 'min:1']
        ]);

        try {
            $data = $request->all();

            $value = $data['value'];

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

            $exceptionCode = $e->getCode();
            if (!in_array($exceptionCode, array_keys(Response::$statusTexts))) {
                $exceptionCode = 500;
            }

            if (!empty($paymentService)) {
                $paymentService->paymentFail($payment);
            }

            return response()->json([
                'message' => $e->getMessage()
            ], $exceptionCode);
        }
    }
}