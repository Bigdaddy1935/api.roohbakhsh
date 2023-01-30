<?php

namespace App\Http\Controllers;

use App\Interfaces\SearchRepositoryInterface;
use App\Models\Notification;
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
        $model_type=$request->model_type;
        $model_id=$request->model_id;
        $this->appNotificationController->sendWebNotification($title,$body);

      $saveNotify=  Notification::query()->create([
            'title'=>$title,
            'body'=>$body,
          'picture'=>$picture,
          'model_type'=>$model_type,
          'model_id'=>$model_id,
        ]);


        return response()->json([
            'message'=>'اعلان با موفقیت ارسال شد',
            'notification'=>$saveNotify,
        ]);

    }

    public function getNotify()
    {
       $notifications =Notification::all();

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



}