<?php

namespace App\Http\Controllers;


use App\Models\Zarinpal;
use App\Repositories\SeminarRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;
use SoapClient;

class SeminarController extends Controller
{

    protected SeminarRepository $seminarRepository;

    public function __construct(SeminarRepository $seminarRepository)
    {
        $this->seminarRepository = $seminarRepository;
    }

    public function SeminarRegister(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|max:11|unique:seminars,phone',
            'firstname'=>'required',
            'lastname'=>'required',
            'amount'=>'required',
            'authority'=>'required',
            'user_count'=>'required'
        ]);


        $data=[
                'phone' => $request->phone,
                'fullname'=>$request->firstname.','.$request->lastname,
                'amount' => $request->amount,
                'authority' => $request->authority,
                'user_count'=>$request->user_count,
            ];

      $res=  $this->seminarRepository->create($data);

        return response()->json($res);
    }


    public function ZarinpalPay(Request $request)
    {

        $request->validate([
            'phone' => 'required|string|max:11|unique:seminars,phone',
        ]);

        $data=$request->all();
        $invoice=new Invoice();
        $amount=$data['amount'];
        $invoice->amount($amount);
        $invoice->detail(['اکادمی سید کاظم روحبخبش'=>'سمینار تهران']);


        return  Payment::callbackUrl('https://roohbakhshac.ir/seminar/verify')->purchase($invoice,function($driver, $transactionId ) use ($amount) {
            $data=[
                'amount'=>$amount,
                'authority'=>$transactionId,
            ];
            Zarinpal::query()->create($data);

        })->pay()->toJson();
    }

    public function VerifyZarinpalPaid(Request $request): \Illuminate\Http\JsonResponse
    {
        $authority = $request->input('Authority');
        $zarinpal=DB::table('zarinpals')->where('authority',$authority)->first();
       $amount=$request->amount;
        $response = Payment::amount($amount)->transactionId($authority)->verify();

        if ($response) {
            $client = new SoapClient("https://ippanel.com/class/sms/wsdlservice/server.php?wsdl");
            $user = "ghasem13741374";
            $pass = "uLhN23sHvH20@";
            $fromNum = "+983000505";
            $toNum = $request->phone;
            $pattern_code = "bqwdu4ir4jwgylp";
            $input_data = array(
                'name' => $request->lastname
            );
            $client->sendPatternSms($fromNum, $toNum, $user, $pass, $pattern_code, $input_data);
            return response()->json($response->getReferenceId());
        }
        return response()->json([
            'message'=>'پرداخت ناموفق'
        ],404);

    }
}
