<?php

namespace App\Http\Controllers;

use App\Models\Seminar;
use App\Models\Zarinpal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;
use SoapClient;

class SeminarController extends Controller
{

    public function SeminarRegister(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:11|unique:seminars,phone',
            'amount' => 'required',
            'authority' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
            'user_count'=>'required'
        ]);

        $data =
            [
                'phone' => $request->phone,
                'fullname' => $request->firstname . ',' . $request->lastname,
                'amount' => $request->amount,
                'authority' => $request->authority,
                'user_count'=>$request->user_count
            ];

        $registered = Seminar::query()->create($data);

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

        $amount=$request->amount;
        $response = zarinpal()
            ->merchantId('845b5d38-3c62-11ea-b338-000c295eb8fc') // تعیین مرچنت کد در حین اجرا - اختیاری
            ->amount($amount) // مبلغ تراکنش
            ->request()
            ->description('ثبت نام در سمینار سید کاظم روحبخش') // توضیحات تراکنش
            ->callbackUrl('https://roohbakhshac.ir/seminar/verify') // آدرس برگشت پس از پرداخت
            ->send();

        if (!$response->success()) {
            return $response->error()->message();
        }


$data=[
    'amount'=>$amount,
    'authority'=>$response->authority()
];

        Zarinpal::query()->create($data);

// هدایت مشتری به درگاه پرداخت
       return response()->json([
           'action'=>$response->url()
       ]);
    }

    public function VerifyZarinpalPaid(Request $request)
    {
        $authority = $request->input('Authority');
        $zarinpal=DB::table('zarinpals')->where('authority',$authority)->first();
        $amount=$zarinpal->amount;
        $response = zarinpal()
            ->merchantId('845b5d38-3c62-11ea-b338-000c295eb8fc') // تعیین مرچنت کد در حین اجرا - اختیاری
            ->amount($amount)
            ->verification()
            ->authority($authority)
            ->send();

        if ($response->success()) {
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
            return response()->json($response->referenceId());
        }
            return $response->error()->message();

    }
}
