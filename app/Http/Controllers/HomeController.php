<?php

namespace App\Http\Controllers;

use App\Interfaces\SearchRepositoryInterface;
use App\Models\Article;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Tutorial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $searchable=[];
    protected SearchRepositoryInterface $searchRepository;
    protected AppNotificationController $appNotificationController;


    public function __construct(SearchRepositoryInterface $searchRepository , AppNotificationController $appNotificationController)
    {
        $this->searchRepository = $searchRepository;
        $this->appNotificationController = $appNotificationController;
    }
    public function search(Request $request): JsonResponse
    {

        $user= auth('sanctum')->id();
        $req=$request->input('search');
        $first=$request->first;
        if($first == 'yes') {

            $course =$this->searchRepository->SearchInCourseFirstWord($req,$user);
            $lesson =$this->searchRepository->SearchInLessonFirstWord($req,$user);
            $article =$this->searchRepository->SearchInArticleFirstWord($req,$user);
            $product=$this->searchRepository->SearchInProductFirstWord($req,$user);

            for ($i=0;$i<count($product);$i++){
                $product[$i]['model']='product';
            }
            for ($i=0;$i<count($course);$i++){
                $course[$i]['model']='courses';
            }
            for ($i=0;$i<count($lesson);$i++){
                $lesson[$i]['model']='lessons';
            }
            for ($i=0;$i<count($article);$i++){
                $article[$i]['model']='articles';
            }


            return response()->json([
                'course'=>$course,
                'lesson'=>$lesson,
                'article'=>$article,
                'product'=>$product
            ]);
        }
        else{
            $course=$this->searchRepository->SearchInCourse($req,$user);
            $lesson=$this->searchRepository->SearchInLesson($req,$user);
            $article=$this->searchRepository->SearchInArticle($req,$user);
            $product=$this->searchRepository->SearchInProduct($req,$user);

            for ($i=0;$i<count($course);$i++){
                $course[$i]['model']='courses';
            }
            for ($i=0;$i<count($lesson);$i++){
                $lesson[$i]['model']='lessons';
            }
            for ($i=0;$i<count($article);$i++){
                $article[$i]['model']='articles';
            }
            for ($i=0;$i<count($product);$i++){
                $product[$i]['model']='products';
            }




            return response()->json([
                'course'=>$course,
                'lesson'=>$lesson,
                'article'=>$article,
                'product'=>$product,
            ]);
        }
    }

    public function showTutorial()
    {
       $tutorial= Tutorial::all();


       return response()->json($tutorial);
    }
    public function SendNotify(Request $request)
    {

        $request->validate([
            'title'=>'required',
            'body'=>'required',
        ]);

        $title=$request->title;
        $body=$request->body;
        $picture=$request->picture;
//        $model_type=$request->model_type;
//        $model_id=$request->model_id;
        $this->appNotificationController->sendWebNotification($title,$body);


        $notify=new Notification;
        $notify->title=$title;
        $notify->body=$body;
        $notify->picture=$picture;

        if($request->course_id != null){
            $course=Course::query()->find($request->course_id);
            $course->notifications()->save($notify);
        }
        elseif ($request->lesson_id != null){
            $lesson=Lesson::query()->find($request->lesson_id);
            $lesson->notifications()->save($notify);
        }
        elseif ($request->article_id != null){
            $article=Article::query()->find($request->article_id);
            $article->notifications()->save($notify);
        }elseif ($request->product_id != null){
            $product=Product::query()->find($request->product_id);
            $product->notifications()->save($notify);
        }else
        {

             Notification::query()->create([
                'title'=>$title,
                'body'=>$body,
                'picture'=>$picture,
                'model_type'=>$request->model_type,
                'model_id'=>$request->model_id,
            ]);
            return response()->json([
                'message'=>'news notifications send successfully'
            ]);
        }





        return response()->json([
            'message'=>'اعلان با موفقیت ارسال شد',
            'notification'=>$notify,
        ]);

    }

    public function getNotify()
    {
        $user= auth('sanctum')->id();

       $notifications =Notification::query()->with('course',function ($q) use ($user){
        $q ->join("users","users.id",'=',"courses.course_user_id")
            ->select("courses.*","users.fullname")
            ->with("categories")
            ->withCount("lessons")
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->with('lessons',function ($q) use ($user){
                $q->withWhereHas('progress',function ($q) use($user) {
                    $q->where('user_id',$user)->where('percentage','>',0);
                });
            });
    })->with('product' ,function ($q) use ($user){
        $q->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
            $q->where('user_id',$user);
        }])
            ->with('categories')
            ->with(['courses'=>function ($q) {
                $q->join('users','users.id','=','courses.course_user_id')
                    ->select('courses.*','users.fullname')->withCount('lessons');
            }])->with(['related'=>function ( $q){
                $q->with('courses');
            }]);
    })->with('lesson',function ($q)use ($user){
        $q   ->join('users','users.id','=','lessons.user_id')
            ->select('lessons.*','users.fullname')
            ->with('categories')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->withExists(['likers as like'=>function($q)use ($user){
                $q->where('user_id',$user);
            }])
            ->with(['progress'=>function ($q)use ($user){
                $q->where('user_id',$user);
            }]);
    })->with('article',function ($q) use ($user){
        $q  ->join('users','users.id','=','articles.user_id')
            ->select('articles.*','users.fullname')
            ->with('tagged')
            ->with('categories');
    })->orderBy('id','DESC')->get();

       return response()->json($notifications);
    }

    public function delNotify($id): JsonResponse
    {
        $ids=explode(",",$id);
       Notification::destroy($ids);
        return response()->json([
            'message'=>"success",
        ]);
    }

    public function updateNotify(Request $request,$id)
    {
        $request->all();
        $data=[
            'title'=>$request->title,
            'body'=>$request->body,
            'picture'=>$request->picture,
            'model_type'=>$request->model_type,
            'model_id'=>$request->model_id,
        ];

       $result= Notification::query()->where('id',$id)->update($data);

        return response()->json([
            'message'=>'موارد مورد نظر با موفقیت ویرایش شد',
            'notify'=>$result
        ]);
    }


    public function CheckVersion(Request $request)
    {

       $version= $request->version;


       if($version == '2.1.1'){
           return response()->json(true);
       }else{
           return response()->json([
               'link'=>'https://dl.poshtybanman.ir/roohbakhshac(v2.1.1).apk',
               'required'=>'yes',
               'message'=>'رفع مشکل خطاهای سیستمی*اضافه شدن باشگاه مهدیارشو'
           ],201);
       }

    }


    public function questionSearch(Request $request,$id)
    {
        $user= auth('sanctum')->id();
        $req=$request->input('search');

        $lesson=$this->searchRepository->SearchInQuestion($req,$user,$id);


        return response()->json(
            $lesson
        );
        }

    public function clubLessonsSearch(Request $request,$id)
    {
        $user= auth('sanctum')->id();
        $req=$request->input('search');

        $lesson=$this->searchRepository->SearchInClubLessons($req,$user,$id);


        return response()->json(
            $lesson
        );
    }

    public function clubCoursesSearch(Request $request , $id)
    {
        $user= auth('sanctum')->id();
        $req=$request->input('search');

        $lesson=$this->searchRepository->SearchInClubCourses($req,$user,$id);

        return response()->json(
            $lesson
        );
    }


}