<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\Invoice;
use App\Models\Lesson;
use App\Models\Product;
use App\Models\Token;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use SoapClient;


class UserRepository extends Repository implements UserRepositoryInterface
{
    public function model()
    {
        return User::class;
    }

    public function SendSms($phone, $token)
    {
        $client = new SoapClient("https://ippanel.com/class/sms/wsdlservice/server.php?wsdl");
        $user = "ghasem13741374";
        $pass = "uLhN23sHvH20@";
        $fromNum = "+98EVENT";
        $toNum = $phone;
        $pattern_code = "g595hekwz5ojg2e";
        $input_data = array(
            "verification-code" => $token,
            'name'=>$phone
        );
        $client ->sendPatternSms($fromNum, $toNum, $user, $pass, $pattern_code, $input_data);
    }

    public function SavePhoneToken(array $data)
    {
        Token::query()->create($data);
    }

    public function CheckSmsToken($phone, $token)
    {
        return Token::query()->where('phone',$phone)->where('token',$token)->first();
    }

    public function SignIn($username, $password)
    {
        $user=  User::query()->where('username', $username)->first();
        if (! $user ) {
            throw ValidationException::withMessages([
                'username' => ['کاربری با این مشخصات یافت نشد.'],
            ]);
        }elseif ( ! Hash::check($password, $user->password)){
            throw ValidationException::withMessages([
                'password'=>['نام کاربری یا گذرواژه شما صحیح نمیباشد.']
            ]);
        }

        return $user;
    }

    public function SetNewPassword($id, $password)
    {
        User::query()->where('id', $id)->update(['password' => bcrypt($password)]);
    }

    public function upload($file)
    {
        $filenamewithextension = $file->getClientOriginalName();

        //get filename without extension
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

        //get file extension
        $extension = $file->getClientOriginalExtension();

        //filename to store
        $filenametostore = $filename.'_'.uniqid().'.'.$extension;

        //Upload File to external server
        Storage::disk('ftp')->put($filenametostore, fopen($file, 'r+'));

        //Store $filenametostore in the database
        return Storage::disk('ftp')->url('https://dl.poshtybanman.ir/upload/'.$filenametostore);
    }

    public function generateCode()
    {


        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersNumber = strlen($characters);
        $codeLength = 6;

        $code = '';

        while (strlen($code) < 6) {
            $position = rand(0, $charactersNumber - 1);
            $character = $characters[$position];
            $code = $code.$character;
        }

        return $code;

    }


    public function GetCountOfUserProgress($id)
    {
     return   Lesson::query()->withWhereHas('progress',function ($q) use($id) {
            $q->where('user_id',$id)->where('percentage','=',100);
        })->count();
    }

    public function GetIdentificationUser($identification)
    {
      return  User::query()->where('code',$identification)->first();
    }

    public function UserPurchasedProductsCount($user_id)
    {
      return  Invoice::query()->where('user_id',$user_id)->count();
    }

    public function UserPurchasedProducts($user_id)
    {
        return  Product::query()->whereHas('invoices',function ($q)use ($user_id){
            $q->where('user_id',$user_id);
        })->with('courses',function ($q){
            $q->join('users','users.id','=','courses.course_user_id')
                ->select('courses.*','users.fullname')->withCount('lessons');
        })->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user_id){
                $q->where('user_id',$user_id);
            }])->with(['related'=>function ( $q){
            $q->with('courses');
        }])
            ->with('categories')->orderBy('id','DESC')->get();
    }
    public function Teachers()
    {
        return User::query()->where('teacher','=',true)->get();
    }
}