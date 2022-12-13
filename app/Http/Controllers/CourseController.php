<?php

namespace App\Http\Controllers;

use App\Interfaces\CourseRepositoryInterface;
use App\Models\Course;
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



class CourseController extends Controller
{
protected $result=[];
    protected CourseRepositoryInterface $courseRepository;
    protected AppNotificationController $appNotificationController;


    public function __construct(AppNotificationController $appNotificationController,CourseRepositoryInterface $courseRepository)
    {
        $this->courseRepository = $courseRepository;
        $this->appNotificationController = $appNotificationController;
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

       if($course){
           return response()->json($course);
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
     */
    public function get_course_by_id($id): JsonResponse
    {

        $course_id=$this->courseRepository->find($id);
        visits($course_id)->seconds(60)->increment();
        $view_count= visits($course_id)->count();
        $course=$this->courseRepository->GetSpecificCourse($id);

        $progressTotal = 0;
        for($i = 0 ; $i < count($course['lessons']); $i++ ){
           $progressTotal += $course['lessons'][$i]['progress']['percentage'];
        }
        $progressTotal = $progressTotal / $course['lessons_count'];
        $course['courseProgress'] = $progressTotal;

        return response()->json($course);
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





}
