<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Cart;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Contracts\Database\Query\Builder;


class ProductRepository extends Repository implements ProductRepositoryInterface
{

    public function model()
    {
        return Product::class;
    }

    public function GetProductsData()
    {
        $user= auth('sanctum')->id();
        return Product::query()
            ->with(['courses'=>function ($q) {
                $q->join('users','users.id','=','courses.course_user_id')
                    ->select('courses.*','users.fullname')->withCount('lessons');
            }])
            ->with('categories')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->orderBy('id','DESC')
            ->paginate(10);
    }

    public function GetSpecificProduct($id)
    {
        $user= auth('sanctum')->id();

        $ifInCart= Cart::query()->where('user_id',$user)->where('product_id',$id)->exists();
        $ifPaid=Invoice::query()->where('user_id',$user)->where('order_id',$id)->exists();


       if (!$ifPaid){
            $response =  Product::query()
                ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                   $q->where('user_id',$user);
               }])
                ->with('categories')
                ->with(['courses'=>function ($q) {
                    $q->join('users','users.id','=','courses.course_user_id')
                        ->select('courses.*','users.fullname')->withCount('lessons');
                }])->with(['related'=>function ( $q){
               $q->with('courses');
           }])
               ->findOrFail($id);
                if($ifInCart){
                    $response['inCart'] = true;
                }else{
                    $response['inCart']=false;
                }
                $response['paid']=false;
                return $response;

        }else
       {

          $response=  Product::query()
              ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                   $q->where('user_id',$user);
               }])
              ->with('categories')
              ->with(['courses'=>function ($q) {
                  $q->join('users','users.id','=','courses.course_user_id')
                      ->select('courses.*','users.fullname')->withCount('lessons');
              }])
              ->with(['related'=>function ( $q){
                  $q->with('courses');
              }])
               ->findOrFail($id);
           if($ifInCart){
               $response['inCart'] = true;
           }else{
               $response['inCart']=false;
           }
           $response['paid']=true;
                return $response;

       }

    }

    public function GetRelatedOfAnProduct($id)
    {
        //get product from id as input with  category
        $product=Product::with('categories')->find($id);

        //get primary keys from product id and category id in pivot table
        $categories = $product->categories->modelKeys();

        //related product  is products with same category id
     return   Product::query()->whereHas('categories', function ($q) use ($categories) {
            $q->whereIn('categories.id', $categories);

            //we show related products without product that we get from id
        })->where('id', '!=', $product->id)->get();
    }


    public function latestProduct()
    {
        $user= auth('sanctum')->id();
        return Product::query()  ->with(['courses'=>function ($q) {
            $q->withCount('lessons');
        }])
            ->with('categories')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->orderBy('id','DESC')->limit(10)->get();
    }

    public function ProductList()
    {
        return Product::query()->join('courses','courses.id', '=','products.course_id')->select('products.*','courses.course_title')->latest()->get();
    }
}