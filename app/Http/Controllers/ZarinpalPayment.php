<?php

namespace App\Http\Controllers;

use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\ZarinpalRepositoryInterface;
use App\Models\User;
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
    private UserRepositoryInterface $userRepository;

    public function __construct(ZarinpalRepositoryInterface $zarinpalRepository , UserRepositoryInterface $userRepository)
    {
        $this->zarinpalRepository = $zarinpalRepository;
        $this->userRepository = $userRepository;
    }




    public function invoicepage(Request $request)
    {

        $data=$request->all();
        $invoice=new Invoice();
        $amount=$data['amount'];
        $invoice->amount($amount);


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
            $fromNum = "+983000505";
            $toNum = $request->parent_num;
            $pattern_code = "cqaovf26yyhqe4m";
            $input_data = array(
                "verification-code" => $token,
                'username'=>$request->username,
                'password'=>$request->password,
            );
            $client ->sendPatternSms($fromNum, $toNum, $user, $pass, $pattern_code, $input_data);


            $refId=$receipt->getReferenceId();
            $user= User::query()->where('username',$request->username)->first();
            if($user){
                $data=[
                    'authority'=>$refId
                ];

                $id=$user->id;
               $this->userRepository->update($id,$data);
            }

//                User::query()->where('national_code',$request->national_code)->update(['amount'=>$amount,'authority'=>$authority]);

            return response()->json($receipt->getReferenceId());
        }

        else{
            return response()->json([
                'message'=>'پرداخت ناموفق'
            ],404);

        }


    }

    public function table()
    {
        return Zarinpal::all();
    }
}