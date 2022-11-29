<?php

namespace App\Http\Controllers;


use App\Interfaces\LessonRepositoryInterface;
use App\Models\Lesson;
use App\QueryFilters\Categories;
use App\QueryFilters\Course_id;
use App\QueryFilters\Sort;
use App\QueryFilters\Teacher;
use App\QueryFilters\Title;
use App\QueryFilters\User_id;
use App\QueryFilters\Visibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;


class LessonController extends Controller
{
    protected $result=[];
    protected AppNotificationController $appNotificationController;
    protected LessonRepositoryInterface $lessonRepository;


    public function __construct(AppNotificationController $appNotificationController ,LessonRepositoryInterface $lessonRepository)
   {
       $this->appNotificationController = $appNotificationController;
       $this->lessonRepository = $lessonRepository;
   }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * validate and save requests to lessons table
     */
    public function addLesson(Request $request): JsonResponse
    {

       $request->validate([
            'title'=>'required|string|unique:lessons,title',
            'user_id'=> 'required',
            'categories'=>'required',
            'teacher'=>'required',
           'sendNotify'=>'required'
        ]);

        $categories=explode(",",$request->categories);
        $data=$request->all();
         $lessons=  $this->lessonRepository->create($data);


        if($request->sendNotify){
            $this->appNotificationController->sendWebNotification('اکادمی سید کاظم روحبخش'," درس {$request->title} اضافه شد ");
        }
        $lessons->categories()->attach($categories);
        return response()->json($lessons,201);

    }


    /**
     *
     * @return JsonResponse
     *
     *
     * get lessons with categories , course  and author name
     */
    public function getLessons(): JsonResponse
    {
        $getLesson=$this->lessonRepository->GetLessonData();

        if($getLesson){
          return response()->json($getLesson);
        }
        else{
          return response()->json([

              'message'=>'درسی ثبت نشده'

          ],401);
      }


    }


    /**
     * @param $id
     * @return JsonResponse
     *
     *
     * delete lessons from input id
     */
    public function deleteLesson($id): JsonResponse
    {

        $ids=explode(",",$id);
        $this->lessonRepository->delete($ids);
        return response()->json([
            'message'=>"درس مورد نظر با موفقیت حذف شد",
            'id'=>$ids,
        ]);
}


    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     *
     * update lessons from input id
     */
    public function updateLesson(Request $request,$id): JsonResponse
    {


        $validate_data  =$request->all();

        $data=[
            'course_id'=>$request->course_id,
            'user_id'=>$request->user_id,
            'title'=>$request->title,
            'picture'=>$request->picture,
            'teacher'=>$request->teacher,
            'description'=>$request->description,
            'url_video'=>$request->url_video,
            'status'=>$request->status,
            'visibility'=>$request->visibility,
            'code'=>$request->code,
        ];

        //get categories as an array
        $categories=explode(",",$request->categories);
     $lesson=  $this->lessonRepository->update($id,$data);



        //sync new categories with old one
        $lesson->categories()->sync($categories);

        return response()->json([
            'message'=>'درس مورد نظر با موفقیت ویرایش شد',
            'Lesson_id'=>$id,
            'lesson'=>$lesson
        ]);
    }


    public function likeLesson( $id): JsonResponse
    {

        $lesson= $this->lessonRepository->find($id);

        $like=  auth()->user()->toggleLike($lesson);

        if(is_bool($like)){
            return response()->json('deleted');
        }else{
            return response()->json('added',201);
        }

    }

    public function bookmarkLesson($id): JsonResponse
    {
        $lesson=$this->lessonRepository->find($id);

        $bookmark= auth()->user()->toggleBookmark($lesson);


        if(is_bool($bookmark)){
            return response()->json('از لیست شما از حذف شد');
        }else{
            return response()->json('به لیست شما اضافه شد',201);
        }

    }


    public function index(): JsonResponse
    {

        $lessons=app(Pipeline::class)->send(Lesson::query())->through([
            Visibility::class,
            Title::class,
            User_id::class,
            Teacher::class,
            Course_id::class,
            Sort::class,
            Categories::class
        ])
            ->thenReturn()
            ->join('users','users.id','=','lessons.user_id')
            ->select('lessons.*','users.fullname')
            ->with('categories')
            ->with('courses')
            ->paginate(10);


        return response()->json($lessons);
    }



    public function getCourseLessons($id)
    {

        $lessons=$this->lessonRepository->GetLessonsOfAnCourse($id);

     foreach ($lessons as $lesson){
         unset($lesson['url_video']);
     }

        return response()->json($lessons);

    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function get_lesson_by_id($id): JsonResponse
    {

        $lesson_id=$this->lessonRepository->find($id);
        visits($lesson_id)->seconds(15*60)->increment();
        $view_count= visits($lesson_id)->count();


        $lesson=$this->lessonRepository->GetSpecificLesson($id);


       return response()->json([
           'lessons'=>$lesson,
           'visits_score'=>$view_count
       ]);
    }

}
