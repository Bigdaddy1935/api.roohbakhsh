<?php

namespace App\Http\Controllers;


use App\Interfaces\ArticleRepositoryInterface;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\LessonRelatedForArticle;
use App\Models\Notification;
use App\Models\Spider;
use App\QueryFilters\Categories;
use App\QueryFilters\Sort;
use App\QueryFilters\Status;
use App\QueryFilters\Title;
use App\QueryFilters\User_id;
use App\QueryFilters\Visibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;


class ArticleController extends Controller
{
    protected $result=[];
    protected AppNotificationController $appNotificationController;
    protected ArticleRepositoryInterface $articleRepository;


    public function __construct(AppNotificationController $appNotificationController ,ArticleRepositoryInterface $articleRepository)
{

    $this->appNotificationController = $appNotificationController;
    $this->articleRepository = $articleRepository;
}

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     *
     * validate request and save to articles table
     */
    public function addArticle(Request $request): JsonResponse
    {

        $this->validate($request, [
            'title' => 'required|unique:articles,title',
            'user_id'=>'required',
            'categories'=>'required',
            'sendNotify'=>'required'
        ]);



        /**
         * with these lines we can insert tags and categories as an array explode with ','
         */
        $input = $request->all();
        $categories=explode(",",$request->categories);
        $tags = explode(",", $request->tags);

        /**
         *save input request on articles table
         */
        $article = $this->articleRepository->create($input);

        $id=$article->id;
        $lesson_ids=explode(",",$request->lesson_ids);
        $lesson_names=explode(",",$request->lesson_names);


        if($request->lesson_ids != null){
            for($i=0;$i<count($lesson_ids);$i++){
                LessonRelatedForArticle::query()->create([
                    'lesson_name'=>$lesson_names[$i],
                    'lesson_id'=>$lesson_ids[$i],
                    'article_id'=>$id
                ]);
            }
        }




        $article_names=explode(',',$request->article_names);
        $article_ids=explode(',',$request->article_ids);

        if($request->article_ids != null){
            for ($i=0;$i<count($article_names);$i++){
                $article->relatedArticles()->attach([$article_ids[$i]=>['name'=>$article_names[$i]]]);
            }
        }




        /**
         * save categories and tags on pivot tables
         */
         $article->categories()->attach($categories);
         $article->tag($tags);
        if($request->sendNotify){
            $this->appNotificationController->sendWebNotification('اکادمی سید کاظم روحبخش'," مقاله {$request->title} اضافه شد ");
            $notify=new Notification;
            $notify->title='اکادمی سید کاظم روح بخش';
            $notify->body=" مقاله {$request->title} اضافه شد ";
            $notify->picture=$request->picture;
            $article->notifications()->save($notify);
        }


        return response()->json($article,201);
   }


    /**
     *
     * @return JsonResponse
     *
     * get articles with categories and author and tags
     */
    public function getArticles(): JsonResponse
    {

   $article=$this->articleRepository->GetArticlesData();

        if(!$article)
        return response()->json([
            'message'=>'محصولی ثبت نشده'
        ],401);

        return response()->json($article);


   }


    /**
     * @param $id
     * @return JsonResponse
     *
     * get one article with their id and set a view count on it 
     *
     */
    public function get_article_by_id($id): JsonResponse
    {

        $article_id=$this->articleRepository->find($id);
        visits($article_id)->seconds(15*60)->increment();
        $view_count= visits($article_id)->count();
        $article=$this->articleRepository->GetSpecificArticle($id);




        return response()->json([
            'article'=>$article,
            'visits_score'=>$view_count
        ]);

}


    /**
     * @param $id
     * @return JsonResponse
     *
     * destroy ids we get from params in articles table
     */
    public function deleteArticle($id): JsonResponse
    {

        $ids=explode(",",$id);
       $res=  Article::query()->whereIn('id',$ids)->with('bookmarkableBookmarks',function ($q){
           $q->where('bookmarkable_type','App\Models\Article');
       })->with('comments',function ($q){
           $q->where('commentable_type','App\Models\Article');
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
        $this->articleRepository->delete($ids);

        return response()->json([
            'message'=>'مقالات مورد نظر با موفقیت حذف شد',
            'ids'=>$res
        ]);
   }



    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     *
     * edit all request from articles id
     */
    public function updateArticle(Request $request,$id): JsonResponse
    {

        $fields= $request->all();

        $data=[
          'user_id'=>$request->user_id,
          'title'=>$request->title,
            'description'=>$request->description,
            'picture'=>$request->picture,
            'status'=>$request->status,
            'visibility'=>$request->visibility,
            'code'=>$request->code,
        ];
        $tags = explode(",", $request->tags);

        $categories=explode(",",$request->categories);

        $article= $this->articleRepository->update($id,$data);

        $lesson_ids=explode(",",$request->lesson_ids);
        $lesson_names=explode(",",$request->lesson_names);



        if($request->lesson_ids != null){
            LessonRelatedForArticle::query()->where('article_id','=',$id)->delete();
            for($i=0;$i<count($lesson_ids);$i++){
                LessonRelatedForArticle::query()->create([
                    'lesson_name'=>$lesson_names[$i],
                    'lesson_id'=>$lesson_ids[$i],
                    'article_id'=>$id
                ]);
            }
        }


        $article_names=explode(',',$request->article_names);
        $article_ids=explode(',',$request->article_ids);

        $article->relatedArticles()->detach();
        if($request->article_ids != null){
            for ($i=0;$i<count($article_names);$i++){
                $article->relatedArticles()->attach($article_ids[$i],['name'=>$article_names[$i]]);
            }
        }




        /**
         * edit categories and tags we get in inputs and save them in pivot table with sync method
         */
        $article->categories()->sync($categories);
        $article->retag($tags);
        return response()->json([
           'message'=>'مقاله مورد نظر با موفقیت ویرایش شد',
            'article_id'=>$id,
            'article'=>$fields
        ]);


   }



    /**
     * @param $id
     * @return JsonResponse
     *
     * get article id and authenticated user for like article
     */
    public function likeArticle( $id): JsonResponse
    {

        $article= $this->articleRepository->find($id);
        $like=  auth()->user()->toggleLike($article);

        return response()->json($like);

    }


    /**
     * @param $id
     * @return JsonResponse
     *
     * authenticated user can bookmark  by model id
     */
    public function bookmarkArticle($id): JsonResponse
    {
        $article=$this->articleRepository->find($id);

        $bookmark= auth()->user()->toggleBookmark($article);


        if(is_bool($bookmark)){
            return response()->json('از لیست شما از حذف شد');
        }else{
            return response()->json('به لیست شما اضافه شد',201);
        }

    }


    /**
     * @return JsonResponse
     *
     * create a filter search by query filters
     */
    public function index(): JsonResponse
    {
//send model to pipeline and do that middlewares to the model then return what we want from it
       $articles=app(Pipeline::class)->send(Article::query())->through([
           Status::class,
           Sort::class,
           Visibility::class,
           Title::class,
           User_id::class,
           Categories::class,
       ])
           ->thenReturn()
           ->join('users','users.id','=','articles.user_id')
            ->select('articles.*','users.fullname')
            ->with('categories')
           ->with('tagged')
           ->orderBy('id','DESC')
           ->paginate(10);
            return response()->json($articles);
}

    public function ArticlesCount()
    {
      $result= $this->articleRepository->ArticlesCount();

      return response()->json([
          'ArticlesCount'=>$result
      ]);
    }

    public function ArticlesTags(Request $request)
    {
        $tags=$request->tags;
        $user= auth('sanctum')->id();
        $result=$this->articleRepository->ArticlesFromTag($tags,$user);
        return response()->json($result);
    }

    public function list()
    {
        $list = $this->articleRepository->ArticleList();

        return response()->json($list);
    }
}
