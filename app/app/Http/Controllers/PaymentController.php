<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Payment;

class PaymentController extends Controller
{
    const AUTHORIZED = "Autorizado";
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
        
        DB::beginTransaction();

        if(!$this->paymentAuthorization($payment)){
            return response()->json(["message"=>"payment not authorized"], 400)
                ->header('Content-Type', 'application/json');
        }
        try{
            $payer['wallet'] -= $value;
            $payer->update();

            $payee['wallet'] += $value;
            $payee->update();

            $paymentData['status'] = "Done";
            $payment->update($paymentData);

            return response()->json($payment, 200)->header('Content-Type', 'application/json');
        
        } catch(Exception $e) {
            DB::rollback();
            return response()->json($e, 400)
                ->header('Content-Type', 'application/json');
        }
        DB::commit();
    }

    private function paymentAuthorization($payment){
        
        $url = "https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6";
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        $responseObj = json_decode($response);
        curl_close($curl);

        return $responseObj->message == self::AUTHORIZED;
    }
}