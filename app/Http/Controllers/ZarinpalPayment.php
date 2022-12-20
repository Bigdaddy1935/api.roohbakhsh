<?php

namespace App\Http\Controllers;

use App\Interfaces\ZarinpalRepositoryInterface;
use App\Models\Zarinpal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class ZarinpalPayment
{

    protected ZarinpalRepositoryInterface $zarinpalRepository;

    public function __construct(ZarinpalRepositoryInterface $zarinpalRepository)
    {
        $this->zarinpalRepository = $zarinpalRepository;
    }




    public function invoicepage(Request $request)
    {
        $data=$request->all();
        $invoice=new Invoice();
        $amount=$data['amount'];
        $invoice->amount($amount);
        $invoice->detail(['خرید محصول سید کاظم روحبخش'=>'محصول اعتقادی اول']);

      return  Payment::purchase($invoice,function($driver, $transactionId ) use ($amount) {
          $data=[
              'amount'=>$amount,
              'authority'=>$transactionId,
          ];
            $this->zarinpalRepository->create($data);

        })->pay()->toJson();


    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyInvoice(Request $request): JsonResponse
    {


        $authority = $request->input('Authority');
        $zarinpal=DB::table('zarinpals')->where('authority',$authority)->first();
        $amount=$zarinpal->amount;

            $receipt = Payment::amount($amount)->transactionId($authority)->verify();

            if($receipt){
                return response()->json($receipt->getReferenceId());
            }
            // You can show payment referenceId to the user.
        return response()->json([
            'message'=>'پرداخت ناموفق'
        ],404);


    }
}