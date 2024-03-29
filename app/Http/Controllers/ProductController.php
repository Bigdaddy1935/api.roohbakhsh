<?php

namespace App\Http\Controllers;

use App\Interfaces\CourseRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Notification;
use App\Models\Product;
use App\QueryFilters\Categories;
use App\QueryFilters\Sort;
use App\QueryFilters\Teacher;
use App\QueryFilters\Title;
use App\QueryFilters\User_id;
use App\QueryFilters\Visibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Validation\ValidationException;


class ProductController extends Controller
{
    protected $url;
    protected AppNotificationController $appNotificationController;
    protected ProductRepositoryInterface $productRepository;
    private CourseRepositoryInterface $courseRepository;


    public function __construct(AppNotificationController $appNotificationController , ProductRepositoryInterface $productRepository,CourseRepositoryInterface $courseRepository)
    {

        $this->appNotificationController = $appNotificationController;
        $this->productRepository = $productRepository;
        $this->courseRepository = $courseRepository;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     *
     *
     * validate and save request for products table
     */
    public function addProduct(Request $request): JsonResponse
    {

        $this->validate($request, [
            'tiny_desc'=>'required',
            'duration'=>'required',
            'course_id'=>'required',
            'categories'=>'required',
            'sendNotify'=>'required',
        ]);


        //change category ,tags ,file_id to an array
        $tags = explode(",", $request->tags);
        $categories=explode(",",$request->categories);
        $related_product_id=explode(',',$request->related);

        $product = $this->productRepository->create([
            'tiny_desc'=>$request->tiny_desc,
            'price'=>$request->price == null ? null :$request->price,
            'duration'=>$request->duration,
            'type'=>$request->type,
            'price_discount'=>$request->price_discount == null ? null :$request->price_discount,
            'course_id'=>$request->course_id,
        ]);

        if($request->related){
            $product->related()->attach($related_product_id);
        }

        $product->categories()->attach($categories);
        $product->tag($tags);

        if($request->sendNotify){
            $this->appNotificationController->sendWebNotification('اکادمی سید کاظم روحبخش'," محصول {$request->title} اضافه شد ");
            $notify=new Notification;
            $notify->title='اکادمی سید کاظم روح بخش';
            $notify->body=" محصول {$request->title} اضافه شد ";
            $notify->picture=$request->picture;
            $product->notifications()->save($notify);
        }
        return response()->json($product,201);
  }


    /**
     * @return JsonResponse
     *
     * get all products with their files and categories
     *
     */
    public function getProducts(): JsonResponse
    {
        $product=$this->productRepository->GetProductsData();

        if(!$product)
            return response()->json([
                'message'=>'محصولی ثبت نشده'
            ],401);

        return response()->json($product);
  }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function get_product_by_id($id): JsonResponse
    {

        $product_id=$this->productRepository->find($id);
        visits($product_id)->seconds(15*60)->increment();
        $view_count= visits($product_id)->count();
        $product=$this->productRepository->GetSpecificProduct($id);


        return response()->json([
            'product'=>$product,
            'visits_score'=>$view_count,

        ]);

     }

    /**
     * @param $id
     * @return JsonResponse
     *
     * get related products as id of main product
     */
    public function relatedProducts( $id): JsonResponse
    {



        $relatedProducts=$this->productRepository->GetRelatedOfAnProduct($id);
        return response()->json([
         'relatedPosts'=>$relatedProducts
     ]);


}


    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     *
     * validate and set new inputs into product table (edit)
     */
    public function updateProducts(Request $request ,$id): JsonResponse
    {

        //request tables into array
        $tags=explode(",",$request->tags);
        $categories=explode(",",$request->categories);
        $related_products_id=explode(",",$request->related);
        //find input id and update fields of product
        $data=[
            'type'=>$request->type,
            'price'=>$request->price == null ? null :$request->price,
            'price_discount'=>$request->price_discount == null ? null :$request->price_discount,
            'duration'=>$request->duration,
            'tiny_desc'=>$request->tiny_desc,
            'course_id'=>$request->course_id
        ];



        $product=$this->productRepository->update($id,$data);


        if($request->related){
         $product->related()->sync($related_products_id);
        }
        //save validation into pivot tables
        $product->tag($tags);
        $product->categories()->sync($categories);

        return response()->json([
           'message'=>'محصول مورد نظر با موفقیت ویرایش شد',
           'product_id'=>$id,
           'product'=>$product
        ]);


  }


    /**
     * @param $id
     * @return JsonResponse
     *
     * delete products with input ids
     */
    public function deleteProducts($id): JsonResponse
    {
        $ids=explode(",",$id);

       $this->courseRepository->delete($ids);

        return response()->json([
           'message'=>'محصولات مورد نظر با موفقیت حذف شدند',
            'product_ids'=>$ids,
        ]);
  }

    public function likeProduct($id): JsonResponse
    {

        $product= $this->productRepository->find($id);

        $like=  auth()->user()->toggleLike($product);

        return response()->json($like);

    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function bookmarkProduct($id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        $bookmark = auth()->user()->toggleBookmark($product);


        if (is_bool($bookmark)) {
            return response()->json('از لیست شما از حذف شد');
        } else {
            return response()->json('به لیست شما اضافه شد', 201);
        }
    }

    public function index(): JsonResponse
    {

        $product=app(Pipeline::class)->send(Product::query())->through([

            Sort::class,
            Visibility::class,
            Title::class,
            User_id::class,
            Teacher::class,
            Categories::class,

        ])
            ->thenReturn()
            ->join('users','users.id','=','products.user_id')
            ->select('products.*','users.fullname')
            ->with('categories')
            ->with('files')
            ->orderBy('id','DESC')
            ->paginate(10);


        return response()->json($product);
    }

    public function newProduct()
    {

      $newPro=  $this->productRepository->latestProduct();

      return response()->json($newPro);
    }

    public function list()
    {
      $result= $this->productRepository->ProductList();

      return response()->json($result);
    }

    public function productCount()
    {
       $result= $this->productRepository->ProductsCount();
        return response()->json([
            'ProductsCount'=>$result
        ]);
    }


}
