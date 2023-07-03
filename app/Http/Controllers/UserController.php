<?php /** @noinspection ALL */

namespace App\Http\Controllers;


use App\Interfaces\ArticleRepositoryInterface;
use App\Interfaces\CourseRepositoryInterface;
use App\Interfaces\LessonRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\ProgressRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Article;
use App\Models\Course;
use App\Models\Token;
use App\Models\User;
use App\Models\VideoProgressBar;
use App\QueryFilters\Fullname;
use App\QueryFilters\Gender;
use App\QueryFilters\Role;
use App\QueryFilters\Sort;
use App\QueryFilters\Username;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Stephenjude\Wallet\Exceptions\InsufficientFundException;
use Stephenjude\Wallet\Exceptions\InvalidAmountException;
use SoapClient;
use Symfony\Component\Console\Helper\Table;

class UserController extends Controller
{

    protected $url;
    protected $result=[];
    protected UserRepositoryInterface $userRepository;
    protected ProgressRepositoryInterface $progressRepository;
    protected CourseRepositoryInterface $courseRepository;
    protected LessonRepositoryInterface $lessonRepository;
    protected ProductRepositoryInterface $productRepository;
    protected ArticleRepositoryInterface $articleRepository;


    public function __construct(
        UserRepositoryInterface $userRepository ,
        ProgressRepositoryInterface $progressRepository ,
        CourseRepositoryInterface $courseRepository ,
        LessonRepositoryInterface $lessonRepository ,
        ProductRepositoryInterface $productRepository,
        ArticleRepositoryInterface $articleRepository,
    )
    {
        $this->userRepository = $userRepository;
        $this->progressRepository = $progressRepository;
        $this->courseRepository = $courseRepository;
        $this->lessonRepository = $lessonRepository;
        $this->productRepository = $productRepository;
        $this->articleRepository = $articleRepository;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     *
     * send auth code sms to users phone
     */
    public function addPhone(Request $request): JsonResponse
    {
        $request->validate([
            'phone'=>'required|max:11|unique:users,phone'
        ]);

        $this->userRepository->SendSms($request->phone ,$token= rand(1000,9999));

        $data=[
                'phone'=>$request->phone,
                'token'=>$token
            ];

        $this->userRepository->SavePhoneToken($data);

        return response()->json([
            'message'=>'کد احراز هویت شما ارسال شد',

        ]);

    }


    /**
     * @param Request $request
     * @return JsonResponse
     *
     * get auth sms code from user
     */
    public function verifyPhone(Request $request): JsonResponse
    {

            $request->validate([
            'phone'=>'required',
            'token'=>'required|max:4'
        ]);

        $result=  $this->userRepository->CheckSmsToken($request->phone,$request->token);

         if($result){
             Token::query()->where('phone',$request->phone)->where('token',$request->token)->delete();
             return response()->json('کد احراز هویت شما با موفقیت تایید شد');

         }

         return response()->json('کد احراز هویت شما نامعتبر میباشد.',422);

    }



    /**
     * @param Request $request
     * @return JsonResponse
     *
     * register users (in all routes)
     */
    public function register(Request $request): JsonResponse
    {


     $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:8',
            'phone'=>'required|string|max:11|unique:users,phone',
            'email'=>'unique:users,email',
        ]);
        if(!empty($request->file('picture'))){
            $this->url=   $this->userRepository->Upload($request->file('picture'));
        }
        $identification=$request->IdentificationCode;

      if($identification != null) {
          $IdentificationUser = $this->userRepository->GetIdentificationUser($identification);
          if($IdentificationUser){
              $IdentificationUser->deposit(40000);
              $code= $this->userRepository->generateCode();
              $data=[
                  'username' =>$request->username,
                  'password' => bcrypt($request->password),
                  'approved'=>$request->approved,
                  'phone'=>$request->phone,
                  'fullname'=>$request->firstname.','.$request->lastname,
                  'email'=>$request->email,
                  'role'=>$request->role,
                  'picture'=> $this->url ,
                  'gender'=>$request->gender,
                  'national_code'=>$request->national_code,
                  'birthday'=>$request->birthday,
                  'born_place'=>$request->born_place,
                  'status_users'=>$request->status_users,
                  'score'=>$request->score,
                  'code'=>$code,
                  'wallet_balance'=>100000,
                  'about_me'=>$request->about_me,
                  'bio_photo'=>$request->bio_photo,
                  'address'=>$request->address,
                  'postal'=>$request->postal,
                  'parent_num'=>$request->parent_num,
                  'messenger_num'=>$request->messenger_num,
                  'amount'=>$request->amount,
                  'authority'=>$request->authority,
                  'city'=>$request->city,
                  'state'=>$request->state,
                  'club_type'=>$request->club_type,
                  'register_club_from'=>$request->register_club_from,
                  'employee_num'=>$request->employee_num,
                  'relation'=>$request->relation,

              ];

              $user= $this->userRepository->create($data);
              $user->deposit(40000);
              return response()->json( $user,201);
      }else {

              return response()->json($res=[
                  'message'=>'کد معرف صحیح نمیباشد.'
              ],404);
          }
      }else {
          $code= $this->userRepository->generateCode();
          $data=[
              'username' =>$request->username,
              'password' => bcrypt($request->password),
              'approved'=>$request->approved,
              'phone'=>$request->phone,
              'fullname'=>$request->firstname.','.$request->lastname,
              'email'=>$request->email,
              'role'=>$request->role,
              'picture'=> $this->url ,
              'gender'=>$request->gender,
              'national_code'=>$request->national_code,
              'birthday'=>$request->birthday,
              'born_place'=>$request->born_place,
              'status_users'=>$request->status_users,
              'score'=>$request->score,
              'code'=>$code,
              'wallet_balance'=>100000,
              'teacher'=>$request->teacher,
              'about_me'=>$request->about_me,
              'bio_photo'=>$request->bio_photo,
              'address'=>$request->address,
              'postal'=>$request->postal,
              'parent_num'=>$request->parent_num,
              'messenger_num'=>$request->messenger_num,
              'amount'=>$request->amount,
              'authority'=>$request->authority,
              'city'=>$request->city,
              'state'=>$request->state,
              'club_type'=>$request->club_type,
              'register_club_from'=>$request->register_club_from,
              'employee_num'=>$request->employee_num,
              'relation'=>$request->relation,

          ];
          $user= $this->userRepository->create($data);
          return response()->json( $user,201);
      }

      }










    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     *
     * login users and create token as device_name
     */
    public function login_app(Request $request ): JsonResponse
    {


        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:8',
            'device_name'=>'required',
        ]);



        $device_name='device'.'='.$request->device_name;
        $user= $this->userRepository->SignIn($request->username,$request->password);

        if(!$user){
            return response()->json([
                'message'=>'نام کاربری یا گذرواژه صحیح نمیباشد'
            ],400);
        }else{
            $user->tokens()->where('name','LIKE','device%')->delete();
            $token=  $user->createToken($device_name)->plainTextToken;

//get last user login at and user ip and save to table
            $user->update([
                'last_login_at'=> Carbon::now()->toDateTimeString(),
                'last_login_ip'=>$request->getClientIp(),
            ]);



            return response()->json([
                'message'=>'ورود با موفقیت انجام شد',
                'user'=>$user,
                'Token'=>$token ,
            ]);

        }



      }



    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     *
     * login users and create token as their username
     */
    public function login_cms(Request $request): JsonResponse
    {

       $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        $username='web'.'='.$request->username;

        $user= $this->userRepository->SignIn($request->username,$request->password);
        $token= $user->createToken($username)->plainTextToken;

        return response()->json([

            'message'=>'ورود با موفقیت انجام شد',
            'user'=>$user,
            'Token'=>$token,
        ]);

    }


    /**
     * @return JsonResponse
     *
     * destroy tokens of user for logout
     */
    public function logout(Request $request): JsonResponse
    {

       $type= $request->type;

       if($type =="web"){
           auth('sanctum')->user()->tokens()->where('name','LIKE','web%')->delete();
       }else{
           auth('sanctum')->user()->tokens()->Where('name','LIKE','device%')->delete();
       }

        return response()->json([
          'message'=>'logout',
      ]);
    }




    /**
     * @return JsonResponse
     *
     * show all users
     */
    public function getUsers(): JsonResponse
    {


        $getUsers=$this->userRepository->GetTeachersAndAuthors();

        if(!$getUsers)
        {
            return response()->json([
                'message'=>'هیچ کاربری ثبت نشده'
            ],401);

        }
        return response()->json($getUsers);
    }




    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     *
     *
     * edit users fields
     */
    public function updateUsers(Request $request,$id): JsonResponse
    {
        $request->all();

       $url=$request->pictureurl;
        $file= $request->file('picture');

        if(!empty($file)){
            $this->url=  $this->userRepository->Upload($file);
        }


            $data=[
                'username' =>$request->username,
                'approved'=>$request->approved,
                'phone'=>$request->phone,
                'fullname'=>$request->firstname.','.$request->lastname,
                'email'=>$request->email,
                'role'=>$request->role,
                'picture'=> $file ?$this->url:$url,
                'gender'=>$request->gender,
                'national_code'=>$request->national_code,
                'birthday'=>$request->birthday,
                'born_place'=>$request->born_place,
                'status_users'=>$request->status_users,
                'score'=>$request->score,
                'teacher'=>$request->teacher,
                'about_me'=>$request->about_me,
                'bio_photo'=>$request->bio_photo,
                'address'=>$request->address,
                'postal'=>$request->postal,
                'parent_num'=>$request->parent_num,
                'messenger_num'=>$request->messenger_num,
                'amount'=>$request->amount,
                'authority'=>$request->authority,
                'city'=>$request->city,

            ];





        $users= $this->userRepository->Update($id,$data);

        return response()->json([
            'message'=>'اطلاعات کاربر مورد نظر با موفقیت ویرایش شد',
            'user_id'=>$id,
            'user'=>$users
        ]);

    }




    /**
     * @param $id
     * @return JsonResponse
     *
     * delete users with input ids
     */
    public function deleteUsers($id): JsonResponse
    {
        $ids=explode(",",$id);
        $this->userRepository->delete($ids);
        return response()->json([
            'message'=>"کاربر مورد نظر با موفقیت حذف شد",
            'users_id'=>$ids,
        ]);
    }




    /**
     * @return JsonResponse
     *
     * filter search users
     */
    public function index(): JsonResponse
    {

        $articles=app(Pipeline::class)->send(User::query())->through([

            Sort::class,
            Fullname::class,
            Username::class,
            Gender::class,
            Role::class

        ])
            ->thenReturn()
            ->orderBy('id','DESC')
            ->paginate(10);


        return response()->json($articles);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     *
     * get new password and check phone number request exist
     */
    public function resetPassword(Request $request): JsonResponse
    {


        $request->validate([
            'phone'=>'required'
        ]);
        $user=User::query()->where('phone',$request->phone)->first();
       $username= $user->username;
        if($user){
            $token= rand(1000,9999);
            $client = new SoapClient("https://ippanel.com/class/sms/wsdlservice/server.php?wsdl");
            $user = "ghasem13741374";
            $pass = "uLhN23sHvH20@";
            $fromNum = "+983000505";
            $toNum = $request->phone;
            $pattern_code = "g595hekwz5ojg2e";
            $input_data = array(
                "verification-code" => $token,
                'name'=>$username
            );
            $client ->sendPatternSms($fromNum, $toNum, $user, $pass, $pattern_code, $input_data);

//            $id= $user->id;
//            $this->userRepository->SetNewPassword($id,$request->password);
            $data=[
                'phone'=>$request->phone,
                'token'=>$token
            ];

            $this->userRepository->SavePhoneToken($data);

            return response()->json([
                'message'=>'کد اهراز حویت شما ارسال شد'
            ]);
        }else
        {
            return response()->json([
                'message'=>'کاربری با این شماره تلفن یافت نشد.'
            ],400);
        }

    }

    public function newPass(Request $request)
    {

        $request->validate([
            'phone'=>'required',
            'password'=>'required'
        ]);

       $user= User::query()->where('phone',$request->phone)->first();
     $user_id=   $user->id;

     $this->userRepository->SetNewPassword($user_id,$request->password);

     return response()->json([
         'message'=>'رمز ورود با موفقیت تغییر یافت'
     ]);
    }


    /**
     * @return JsonResponse
     *
     * get likes of an authenticated user
     */
    public function userLikes(): JsonResponse
    {

        $likes=auth()->user()->likes()->with('likeable')->get();

        return response()->json($likes);

    }


    /**
     * @return JsonResponse
     *
     * get bookmarks of an authenticated user
     */
    public function userBookmarks(): JsonResponse
    {

       
        $bookmarks=auth()->user()->bookmarkerBookmarks()->get();
//        $bookmarks['bookmarkable_id'];

        $courses = [];
        $lessons = [];
        $products = [];
        $articles = [];

        for ($i = 0; $i < count($bookmarks); $i++) {
            $model = $bookmarks[$i]['bookmarkable_type'];
            $id = $bookmarks[$i]['bookmarkable_id'];
            if ($model === 'App\\Models\\Course') {
                $courses[] = $this->courseRepository->GetSpecificCourse($id);
            }
            if ($model === 'App\\Models\\Lesson') {
                $lessons[] = $this->lessonRepository->GetSpecificLesson($id);
            }
            if ($model === 'App\\Models\\Product') {
                $products[] = $this->productRepository->GetSpecificProduct($id);
            }
            if($model === 'App\\Models\\Article'){
                $articles[]= $this->articleRepository->GetSpecificArticle($id);
            }
        }
        $bookmarks['courses'] = $courses;
        $bookmarks['lessons'] = $lessons;
        $bookmarks['products'] = $products;
        $bookmarks['articles']= $articles;

        return response()->json($bookmarks);

    }
    /**
     *
     * get user info by their bearer token
     * @return JsonResponse
     */
    public function auth(Request $request): JsonResponse
    {

    $user=    auth('sanctum')->user();

    return response()->json([
        'user'=>$user,
        'Token'=>$request->bearerToken(),
    ]);
}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deposit(Request $request): JsonResponse
    {
        $request->validate([
            'amount'=>'required',
            'type'=>'required',
        ]);

        $user=auth()->user();
        $data=[
            'user_id'=>$user->id,
            'amount'=>$request->amount,
            'type'=>$request->type,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
        ];
        try {

            $deposit=  $user->deposit($request->amount);
            DB::table('deposit_history')->insert($data);
            return response()->json([
                'wallet_balance'=>$deposit
            ]);

        }catch (InvalidAmountException $e){
           return response()->json($e->getMessage()) ;
        }

}


    public function getDepositHistory()
    {
       $user_id= auth()->id();
     $history=   DB::table('deposit_history')->where('user_id',$user_id)->get();

        return response()->json($history);
}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function useBalance(Request $request): JsonResponse
    {

        $request->validate([
            'amount'=>'required'
        ]);



        $user=auth()->user();

        $amount=   $request->amount;
        $balance= $user->balance;


        try {
         $withdraw=   $user->withdraw($request->amount);
            return response()->json([
                'wallet_balance'=>$withdraw
            ]);
        }catch (InsufficientFundException){
           $need= $amount-$balance;
           return response()->json([
               'product price'=>$amount,
               'wallet_balance'=>$balance,
               'need'=>$need
           ]);
        }


}


    /**
     * @return JsonResponse
     */
    public function addScoreForLessonsProgress(): JsonResponse
    {
        $id=  auth('sanctum')->id();
      $count=  $this->userRepository->GetCountOfUserProgress($id);

       $score= $count*10;

        $user = $this->userRepository->find($id);

       $currentScore= $user->score ?? 0;

       $currentScore +=$score;

       $user->forceFill(['score'=>$currentScore])->save();

       return response()->json($currentScore);

    }


    public function lessonsLearned(): JsonResponse
    {

        $user=auth()->id();
        $result=$this->progressRepository->LessonsSeeFull($user);
     return response()->json([
         'lessonLearned'=>$result
     ]);
    }

    public function LessonsSee()
    {
        $user=auth()->id();
        $lessons=$this->progressRepository->CurrentLessonsSee($user);

      $lessonsC=  count($lessons);

        for ($i=0;$i<$lessonsC;$i++) {
            unset($lessons[$i]['lessons'][0]['url_video']);
        }

        return response()->json([
            'lessonSee'=>$lessons
        ]);
    }

    public function PurchasedProductsCount(): JsonResponse
    {
       $user_id= auth()->id();

      $count= $this->userRepository->UserPurchasedProductsCount($user_id);

      return response()->json([
          'message'=>'تعداد محصولات خریداری شده',
          'productsCount'=>$count
      ]);
    }

    public function PurchasedProducts(): JsonResponse
    {

        $user_id=auth()->id();
        $purchased=$this->userRepository->UserPurchasedProducts($user_id);

        return response()->json([
            'message'=>'محصولات خریداری شده',
            'products'=>$purchased
        ]);


    }

    public function userScoreDeposit()
    {

       $user= auth()->user();
        $currentScore= $user->score ?? 0;

       if ($currentScore >= 100){
           $user->deposit(20000);

        $newscore=   $currentScore - 100;

           $user->forceFill(['score'=>$newscore])->save();


       }
       $userScore=$user->score;
        $balance= $user->wallet_balance;
       return response()->json([
           'currentScore'=>$userScore,
           'walletBalance'=>$balance,
       ]);
    }

    public function UsersCount()
    {
     $result=   User::query()->get()->count();

     return response()->json([
         'UsersCount'=>$result
     ]);
    }

    public function CourseProgress()
    {
        $user=auth()->id();
        $res=$this->courseRepository->CurrentCourseSee($user);
        for ($i=0;$i<count($res);$i++){
            $totalProgress = 0;
            $newRes = $res[$i]['lessons'];
            for($j=0;$j < count($newRes); $j++){
                $totalProgress += $newRes[$j]['progress'][0]['percentage'];
            }
            $res[$i]['courseProgress']=sprintf("%.2f",$totalProgress/$res[$i]['lessons_count']);
        }
        return response()->json($res);
    }

    public function CourseProgressFull()
    {
        $user=auth()->id();
        $res=$this->courseRepository->CourseSeeFull($user);
        return response()->json($res);
    }

    public function MahdyarRegister(Request $request)
    {


        $data=[
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'phone'=>$request->phone,
            'fullname'=>$request->firstname.','.$request->lastname,
            'gender'=>$request->gender,
            'national_code'=>$request->national_code,
            'birthday'=>$request->birthday,
            'address'=>$request->address,
            'postal'=>$request->postal,
            'parent_num'=>$request->parent_num,
            'messenger_num'=>$request->messenger_num,
            'amount'=>$request->amount,
            'authority'=>$request->authority,
            'city'=>$request->city,
            'state'=>$request->state,
            'club_type'=>$request->club_type,
            'register_club_from'=>$request->register_club_from,
            'employee_num'=>$request->employee_num,
            'relation'=>$request->relation,
        ];

        $user= User::query()->where('username',$request->username)->first();

        if(!$user && !Hash::check($request->password,$user->password)){
            $users=$this->userRepository->create($data);
        }else
        {
            $id=  $user->id;
            $users=$this->userRepository->update($id,$data);
        }


        $password=$request->password;
        $user=  User::query()->where('username', $request->username)->first();

        if (! $user ) {
            $users=$this->userRepository->create($data);
        }elseif ( $user->username == $request->username && Hash::check($password, $user->password)){
            $users=$this->userRepository->update($user->id,$data);
        }elseif ($user->username == $request->username && !Hash::check($password, $user->password)){
            $users=$this->userRepository->create($data);
        }


        return response()->json([
            'message'=>'ثبت نام با موفقیت انجام شد',
            'user'=>$users
        ]);
    }



    public function MahdyarSms(Request $request)
    {
//        $request->all();
//        $token=$request->refrence_code;
//        $client = new SoapClient("https://ippanel.com/class/sms/wsdlservice/server.php?wsdl");
//        $user = "ghasem13741374";
//        $pass = "uLhN23sHvH20@";
//        $fromNum = "+98EVENT";
//        $toNum = $request->parent_num;
//        $pattern_code = "cqaovf26yyhqe4m";
//        $input_data = array(
//            "verification-code" => $token,
//            'name'=>$request->fullname
//        );
//        $client ->sendPatternSms($fromNum, $toNum, $user, $pass, $pattern_code, $input_data);


    return response()->json([
   'message'=>'پیامک با موفقیت ارسال شد',
   'phone'=>$toNum,
   'name'=>$request->fullname,
   'refrence_code'=>$request->refrence_code
    ]);
    }


    public function getState()
    {


        $result= DB::table('province')->get();

        return response()->json($result);
    }

    public function getCityByStateName($name)
    {
     $states=DB::table('province')->where('name','=',$name)->get();

     foreach ($states as $state){
          $id= $state->id;
     }


     $result=DB::table('city')->where('province_id','=',$id)->get();

     return response()->json($result);
    }


}
