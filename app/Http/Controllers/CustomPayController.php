<?php

namespace App\Http\Controllers;

use App\Models\CustomPay;
use Illuminate\Http\Request;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class CustomPayController extends Controller
{

    public function CustomPayInvoice(Request $request)
    {
        $data=$request->all();
        $invoice=new Invoice();
        $amount=$data['amount'];
        $invoice->amount($amount);

        return  Payment::callbackUrl('https://web.roohbakhshac.ir')->purchase($invoice,function($driver, $transactionId ) use ($amount) {
            $data=[
                'amount'=>$amount,
                'authority'=>$transactionId,
            ];
            CustomPay::query()->create($data);

        })->pay()->toJson();
    }

    public function CustomPayVerify(Request $request)
    {
        $authority=$request->input('Authority');
        $amount=$request->amount;
        $response=Payment::amount($amount)->transactionId($authority)->verify();

        if($response){
         return response()->json([
             'message'=>'پرداخت موفق'
         ]);
        }else{
            return response()->json([
                'message'=>'پرداخت نا موفق'
            ]);
        }
        
    }


}
