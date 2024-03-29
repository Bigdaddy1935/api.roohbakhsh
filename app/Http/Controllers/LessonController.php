<?php

namespace App\Http\Controllers;


use App\Interfaces\LessonRepositoryInterface;
use App\Models\Article;
use App\Models\ArticleRelatedForLesson;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Spider;
use App\Models\VideoProgressBar;
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
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isEmpty;


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
            'sendNotify'=>'required',
            'formats'=>'required'
        ]);



        $categories=explode(",",$request->categories);
        $data=$request->all();
         $lessons=  $this->lessonRepository->create($data);
         $tags = explode(",", $request->tags);

         $id=  $lessons->id;

         $article_names=explode(',',$request->article_names);
         $article_ids=explode(',',$request->article_ids);



         if($request->article_ids != null){
             for($i=0;$i<count($article_ids);$i++){
                 ArticleRelatedForLesson::query()->create([
                     'article_name'=>$article_names[$i],
                     'article_id'=>$article_ids[$i],
                     'lesson_id'=>$id
                 ]);
             }
         }




        $lesson_ids=explode(",",$request->lesson_ids);
        $lesson_names=explode(",",$request->lesson_names);


         if($request->lesson_ids != null){
             for ($i=0;$i<count($lesson_names);$i++){
                 $lessons->relatedLessons()->attach([$lesson_ids[$i]=>['name'=>$lesson_names[$i]]]);
             }
         }







        if($request->sendNotify){
            $this->appNotificationController->sendWebNotification('اکادمی سید کاظم روحبخش'," درس {$request->title} اضافه شد ");
            $notify=new Notification;
            $notify->title='اکادمی سید کاظم روح بخش';
            $notify->body=" درس {$request->title} اضافه شد ";
            $notify->picture=$request->picture;
            $lessons->notifications()->save($notify);
        }
        $lessons->categories()->attach($categories);
        $lessons->tag($tags);
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
    public function deleteLesson($id,$type='id'): JsonResponse
    {

        $ids=explode(",",$id);

        $res=Lesson::query()->whereIn($type,$ids)
            ->with('bookmarkableBookmarks',function ($q){
            $q->where('bookmarkable_type','App\Models\Lesson');
             })->with('comments',function ($q){
            $q->where('commentable_type','App\Models\Lesson');
        })->with('progress')->get()->toArray();

        $bookmarks=[];
        $comments=[];
        $progress=[];
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

        for($i=0;$i<count($res);$i++) {
            $newRes = count($res[$i]['progress']);
            for ($j = 0; $j < $newRes; $j++) {
                $progress[]= $res[$i]['progress'][$j]['id'];
            }
        }


        if(count($bookmarks)!=0){
            DB::table('bookmarks')->whereIn('id',$bookmarks)->delete();
        }
        if(count($comments)!=0){
            Comment::destroy($comments);
        }
        if(count($progress)!=0){
            VideoProgressBar::destroy($progress);
        }
        if($type=='id'){
              $this->lessonRepository->delete($ids);
            }

        return response()->json([
            'message'=>'درس های مورد نظر با موفقیت حذف شد',
            'ids'=>$ids
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


     $request->all();

        $data=[
            'course_id'=>$request->course_id,
            'user_id'=>$request->user_id,
            'title'=>$request->title,
            'picture'=>$request->picture,
            'teacher'=>$request->teacher,
            'description'=>$request->description,
            'url_video'=>$request->url_video,
            'url_ads'=>$request->url_ads,
            'status'=>$request->status,
            'visibility'=>$request->visibility,
            'code'=>$request->code,
            'formats'=>$request->formats,
        ];

        //get categories as an array
        $categories=explode(",",$request->categories);
        $tags = explode(",", $request->tags);

        $lesson=  $this->lessonRepository->update($id,$data);

        $article_names=explode(',',$request->article_names);
        $article_ids=explode(',',$request->article_ids);





        if($request->article_ids != null){
            ArticleRelatedForLesson::query()->where('lesson_id','=',$id)->delete();
            for($i=0;$i<count($article_ids);$i++){
                ArticleRelatedForLesson::query()->create([
                    'article_name'=>$article_names[$i],
                    'article_id'=>$article_ids[$i],
                    'lesson_id'=>$id
                ]);
            }
        }


        $lesson_ids=explode(",",$request->lesson_ids);
        $lesson_names=explode(",",$request->lesson_names);

        $lesson->relatedLessons()->detach();
        if($request->lesson_ids != null){
            for ($i=0;$i<count($lesson_names);$i++){
                $lesson->relatedLessons()->attach($lesson_ids[$i],['name'=>$lesson_names[$i]]);
            }
        }



        //sync new categories with old one
        $lesson->categories()->sync($categories);
        $lesson->retag($tags);

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
            ->orderBy('id','DESC')
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

    public function GetLessonsOfAnCourseNotCompletedProgress($id)
    {
        $lessons=$this->lessonRepository->GetLessonsOfAnCourseNotCompleted($id);

        foreach ($lessons as $lesson){
            unset($lesson['url_video']);
        }
        return response()->json($lessons);

    }

    public function GetLessonsOfAnCourseWithFullProgress($id)
    {
        $lessons=$this->lessonRepository->GetLessonsOfAnCourseFullProgress($id);
        foreach ($lessons as $lesson){
            unset($lesson['url_video']);
        }

        return response()->json($lessons);


    }

    public function getCourseLessonsWithoutPaginate($id): JsonResponse
    {

        $lessons=$this->lessonRepository->GetLessonsOfAnCourseGet($id);
        foreach ($lessons as $lesson){
            unset($lesson['url_video']);
        }

        return response()->json($lessons);

    }

    public function getMediaLessons($id)
    {
        $lessons=$this->lessonRepository->GetLessonsOfAnMedia($id);
        return response()->json($lessons);

    }

    public function getAllLessonsMedia()
    {
        $lessons=$this->lessonRepository->GetLessonsOfAllMedias();

        return response()->json($lessons);
    }
    public function getMahdyarLessons($id)
    {
        $lessons=$this->lessonRepository->GetLessonsOfAnMahdyar($id);
        return response()->json($lessons);

    }

    public function getAllMahdyarLessons()
    {
        $lessons=$this->lessonRepository->GetLessonsOfAllMahdyar();

        return response()->json($lessons);
    }

    public function getKolbeLessons($id)
    {
        $lessons=$this->lessonRepository->GetLessonsOfAnKolbe($id);
        return response()->json($lessons);

    }

    public function getAllKolbeLessons()
    {
        $lessons=$this->lessonRepository->GetLessonsOfAllKolbe();

        return response()->json($lessons);
    }

    public function getTvLessons($id)
    {
        $lessons=$this->lessonRepository->GetLessonsOfAnTv($id);
        return response()->json($lessons);

    }

    public function getAllTvLessons()
    {
        $lessons=$this->lessonRepository->GetLessonsOfAllTv();

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





        $lesson->incrementViewCount();


       return response()->json([
           'lessons'=>$lesson,

           'visits_score'=>$view_count
       ]);
    }

    public function Lcount()
    {
      $result=  $this->lessonRepository->lessonsCount();
      return response()->json([
          'lessonsCount'=>$result
      ]);
    }

    public function list()
    {
      $list=  $this->lessonRepository->lessonsList();

        return response()->json($list);
    }

    public function LessonsTags(Request $request)
    {
        $tags=$request->tags;
        $user=auth('sanctum')->id();
        $result=$this->lessonRepository->LessonsFromTag($tags,$user);
        return response()->json($result);
    }

    public function getPodcasts()
    {
        $podcasts=$this->lessonRepository->GetAllPodcast();

        return response()->json($podcasts);
    }

    public function getMahdyarQuestion(): JsonResponse
    {
        $podcasts=$this->lessonRepository->GetAllMahdyarQuestion();

        return response()->json($podcasts);
    }
}
