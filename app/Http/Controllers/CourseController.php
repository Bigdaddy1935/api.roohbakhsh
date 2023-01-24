<?php

namespace App\Http\Controllers;

use App\Interfaces\CourseRepositoryInterface;
use App\Interfaces\LessonRepositoryInterface;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Lesson;
use App\Models\Product;
use App\Models\VideoProgressBar;
use App\QueryFilters\Type;
use App\QueryFilters\Types;
use App\QueryFilters\Categories;
use App\QueryFilters\CourseTeacher;
use App\QueryFilters\CourseTitle;
use App\QueryFilters\CourseUserId;
use App\QueryFilters\CourseVisibility;
use App\QueryFilters\Sort;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;


class CourseController extends Controller
{
protected $result=[];
    protected CourseRepositoryInterface $courseRepository;
    protected AppNotificationController $appNotificationController;
    protected LessonRepositoryInterface $lessonRepository;


    public function __construct(AppNotificationController $appNotificationController,CourseRepositoryInterface $courseRepository,LessonRepositoryInterface $lessonRepository )
    {
        $this->courseRepository = $courseRepository;
        $this->appNotificationController = $appNotificationController;
        $this->lessonRepository = $lessonRepository;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     *
     *
     */
    public function addCourse( Request $request): JsonResponse
    {
         $request->validate([
            'course_title' => 'required|string|unique:courses,course_title',
            'course_user_id' =>'required',
            'categories'=>'required',
            'course_teacher'=>'required',
             'sendNotify'=>'required'
        ]);

                //get categories as an array from input
        $categories=explode(",",$request->categories);
        $data=$request->all();
        $course=  $this->courseRepository->create($data);


            //save categories in pivot table
        $course->categories()->attach($categories);
        if($request->sendNotify){
            $this->appNotificationController->sendWebNotification('اکادمی سید کاظم روحبخش'," دوره {$request->course_title} اضافه شد ");
        }

        return response()->json($course, 201);
    }


    /**
     * @return JsonResponse
     */
    public function getCourses(): JsonResponse
    {

     $course=$this->courseRepository->GetCoursesData();

        for ($i=0;$i<count($course['data']);$i++){
            $totalProgress = 0;
            $lessonCount = $course['data'][$i]['lessons_count'];
            $newRes = $course['data'][$i]['lessons'];
            for($j=0;$j < count($newRes); $j++){
                $totalProgress += $newRes[$j]['progress'][0]['percentage'];
            }
            if($totalProgress == 0){
                $course['data'][$i]['courseProgress'] = 0.0;
            }else {
                $course['data'][$i]['courseProgress'] = sprintf("%.2f",($totalProgress / $lessonCount));
            }
        }

       if($course){
           return response()->json($course);
       }
       else{
           return response()->json([

               'message'=>'دوره ای ثبت نشده'

           ],401);
       }

    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function get_course_by_id($id): JsonResponse
    {

        $course_id=$this->courseRepository->find($id);
        visits($course_id)->seconds(60)->increment();
        $view_count= visits($course_id)->count();
        $course=$this->courseRepository->GetSpecificCourse($id);

        $progressTotal = 0;
        for($i = 0 ; $i < count($course['lessons']); $i++ ){
           $progressTotal = $course['lessons'][$i]['progress']['percentage']==0?0 :($course['lessons'][$i]['progress']['percentage']);
        }
        $progressTotal = $progressTotal==0?0:  ($progressTotal / $course['lessons_count']);
        $course['courseProgress'] = $progressTotal;

        return response()->json([
            'course'=>$course,
            'visits'=>$view_count
        ]);
    }


    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     *
     *
     * update request as input course id
     */
    public function updateCourse(Request $request,$id): JsonResponse
    {

        $validate_data  =$request->all();

        //get categories as an array

        $data=[
            'course_user_id'=>$request->course_user_id,
            'course_title'=>$request->course_title,
            'description'=>$request->description,
            'type'=>$request->type,
            'course_visibility'=>$request->course_visibility,
            'code'=>$request->code,
            'access'=>$request->access,
            'course_teacher'=>$request->course_teacher,
            'course_status'=>$request->course_status,
            'navigation'=>$request->navigation,
            'picture'=>$request->picture,

        ];
       if($data['type']=='course') {

        if($product=Product::query()->where('course_id',$id)->first()){
            $pro_id=$product['id'];
            Invoice::query()->where('order_id',$pro_id)->delete();
            DB::table('bookmarks')->where('bookmarkable_id',$pro_id)->delete();
           Product::query()->where('course_id',$id)->delete();
        }
       }

//        if($data['type']=='course'){
//           $product= Product::query()->where('course_id',$id)->first()->toArray();
//        $pro_id=$product['id'];
//        Invoice::query()->where('order_id',$pro_id)->delete();
//        DB::table('bookmarks')->where('bookmarkable_id',$pro_id)->delete();
//        Product::query()->where('course_id',$id)->delete();
//        }

        $categories=explode(",",$request->categories);
        $Course = $this->courseRepository->update($id,$data);
        //sync new categories as old one
        $Course->categories()->sync($categories);

        return response()->json([
            'message'=>'دوره مورد نظر با موفقیت ویرایش شد',
            'id'=>$id,
            'Course'=>$Course
        ]);
    }


    /**
     * @param $id
     * @return JsonResponse
     *
     *
     * destroy ids we get from params in course table
     *
     */
    public function deleteCourse($id): JsonResponse
    {
        $ids=explode(",",$id);


        (new LessonController($this->appNotificationController,$this->lessonRepository))->deleteLesson($id,'course_id');
        Lesson::query()->whereIn('course_id',$ids)->delete();
        $res=  Course::query()->whereIn('id',$ids)->with('bookmarkableBookmarks',function ($q){
            $q->where('bookmarkable_type','App\Models\Course');
        })->with('comments',function ($q){
            $q->where('commentable_type','App\Models\Course');
        })->get()->toArray();
        $bookmarks=[];
        $comments=[];

        for($i=0;$i<count($res);$i++) {
            $newRes = count($res[$i]['bookmarkable_bookmarks']);
            for ($j = 0; $j < $newRes; $j++) {
                $bookmarks[]= $res[$i]['bookmarkable_bookmarks'][$j]['id'];
            }
        }


        for($i=0;$i<count($res);$i++) {
            $newRes = count($res[$i]['comments']);
            for ($j = 0; $j < $newRes; $j++) {
                $comments[]= $res[$i]['comments'][$j]['id'];
            }
        }

        if(count($bookmarks)!=0){
            DB::table('bookmarks')->whereIn('id',$bookmarks)->delete();
        }
        if(count($comments)!=0){
            Comment::destroy($comments);
        }


        $this->courseRepository->delete($ids);
        return response()->json([
          'message'=>"دوره مورد نظر با موفقیت حذف شد",
          'id'=>$ids,
      ]);
}



    public function index(): JsonResponse
    {

        $course=app(Pipeline::class)->send(Course::query())->through([
            CourseVisibility::class,
            CourseTitle::class,
            CourseUserId::class,
            CourseTeacher::class,
            Sort::class,
            Categories::class,
            Type::class

        ])
            ->thenReturn()
            ->join('users','users.id','=','courses.course_user_id')
            ->select('courses.*','users.fullname')
            ->with('categories')
            ->orderBy('id','DESC')
            ->paginate(10);


        return response()->json($course);
    }

    public function likeCourse( $id): JsonResponse
    {

       $course= $this->courseRepository->find($id);

   $like=  auth()->user()->toggleLike($course);

        return response()->json($like);

}

    /**
     * @param $id
     * @return JsonResponse
     */
    public function bookmarkCourse($id): JsonResponse
    {

        $course=$this->courseRepository->find($id);

        $bookmark= auth()->user()->toggleBookmark($course);


        if(is_bool($bookmark)){
            return response()->json('از لیست شما از حذف شد');
        }else{
            return response()->json('به لیست شما اضافه شد',201);
        }

    }

    public function list()
    {
      $result=  $this->courseRepository->courselist();
        return response()->json($result);
    }

    public function CoursesCounts()
    {
       $result= $this->courseRepository->CoursesCount();

       return response()->json([
           'coursesCount'=>$result
       ]);
    }

    public function getMedia()
    {
      $course=  $this->courseRepository->getCourseMedia();

        if($course){
            return response()->json($course);
        }
        else{
            return response()->json([

                'message'=>'رسانه ای ثبت نشده'

            ],401);
        }
    }





}
