<?php

namespace App\Http\Controllers;

use App\Interfaces\ZarinpalRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $zarinpal=$this->zarinpalRepository->VerifyZarinpalPayment($authority);
        $amount=$zarinpal['amount'];
        try {

            $receipt = Payment::amount($amount)->transactionId($authority)->verify();
            // You can show payment referenceId to the user.

            return response()->json($receipt->getReferenceId());

        } catch (InvalidPaymentException $exception) {
            /**
            when payment is not verified, it will throw an exception.
            We can catch the exception to handle invalid payments.
            getMessage method, returns a suitable message that can be used in user interface.
             **/
            return response()->json($exception->getMessage(),404);
        }
    }
}