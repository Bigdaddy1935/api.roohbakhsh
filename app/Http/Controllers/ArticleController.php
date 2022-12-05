<?php

namespace App\Http\Controllers;


use App\Interfaces\ArticleRepositoryInterface;
use App\Models\Article;
use App\QueryFilters\Categories;
use App\QueryFilters\Sort;
use App\QueryFilters\Status;
use App\QueryFilters\Title;
use App\QueryFilters\User_id;
use App\QueryFilters\Visibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Validation\ValidationException;



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

        /**
         * save categories and tags on pivot tables
         */
         $article->categories()->attach($categories);
         $article->tag($tags);
        if($request->sendNotify){
            $this->appNotificationController->sendWebNotification('اکادمی سید کاظم روحبخش'," مقاله {$request->title} اضافه شد ");
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
        $this->articleRepository->delete($ids);
        return response()->json([
            'message'=>"مقاله مورد نظر با موفقیت حذف شد",
            'id'=>$ids
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

        /**
         * edit categories and tags we get in inputs and save them in pivot table with sync method
         */
        $article->categories()->sync($categories);
        $article->tag($tags);
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
}
