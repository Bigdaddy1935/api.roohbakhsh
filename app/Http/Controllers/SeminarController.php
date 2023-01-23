<?php

namespace App\Http\Controllers;

use App\Models\Seminar;
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

    public function SeminarRegister(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:11|unique:seminars,phone',
        ]);

        $fullname=$request->firstname.','.$request->lastname;
        $data=[
                'phone' => $request->phone,
                'fullname'=>$fullname,
                'amount' => $request->amount,
                'authority' => $request->authority,
                'user_count'=>$request->user_count,
            ];

        $registered = $this->seminarRepository->create($data);

        return response()->json([
            'message' => 'ثبت نام با موفقیت انجام شد',
            'registered' => $registered
        ]);
    }

    /**
     * @throws \Exception
     */
    public function ZarinpalPay(Request $request)
    {

        $request->validate([
            'phone' => 'required|string|max:11|unique:seminars,phone',
        ]);

        $data=$request->all();
        $invoice=new Invoice();
        $amount=$data['amount'];
        $invoice->amount($amount);
        $invoice->detail(['خرید محصول سید کاظم روحبخش'=>'محصول اعتقادی اول']);


        return  Payment::callbackUrl('https://roohbakhshac.ir/seminar/verify')->purchase($invoice,function($driver, $transactionId ) use ($amount) {
            $data=[
                'amount'=>$amount,
                'authority'=>$transactionId,
            ];
            Zarinpal::query()->create($data);

        })->pay()->toJson();
    }

    public function VerifyZarinpalPaid(Request $request)
    {
        $authority = $request->input('Authority');
        $zarinpal=DB::table('zarinpals')->where('authority',$authority)->first();
       $amount=$request->amount;
        $response = Payment::amount($amount)->transactionId($authority)->verify();

        if ($response) {
            $client = new SoapClient("https://ippanel.com/class/sms/wsdlservice/server.php?wsdl");
            $user = "ghasem13741374";
            $pass = "uLhN23sHvH20@";
            $fromNum = "+98EVENT";
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
