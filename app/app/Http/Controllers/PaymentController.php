<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    public function makeTransaction(Request $request){

        $data = json_encode(["message"=>"Foi"]);
        return response()->json($data, 200)->header('Content-Type', 'application/json');
    }
}