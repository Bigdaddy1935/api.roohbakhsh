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
use SoapClient;

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
                $token=$receipt->getReferenceId();
                $client = new SoapClient("https://ippanel.com/class/sms/wsdlservice/server.php?wsdl");
                $user = "ghasem13741374";
                $pass = "uLhN23sHvH20@";
                $fromNum = "+98EVENT";
                $toNum = $request->parent_num;
                $pattern_code = "cqaovf26yyhqe4m";
                $input_data = array(
                    "verification-code" => $token,
                    'name'=>$request->parent_num
                );
                $client ->sendPatternSms($fromNum, $toNum, $user, $pass, $pattern_code, $input_data);
                return response()->json($receipt->getReferenceId());
            }
            // You can show payment referenceId to the user.
        return response()->json([
            'message'=>'پرداخت ناموفق'
        ],404);


    }
}