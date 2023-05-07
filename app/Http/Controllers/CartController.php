<?php

namespace App\Http\Controllers;

use App\Interfaces\CartRepositoryInterface;


use App\Models\Product;
use Illuminate\Http\JsonResponse;


class CartController extends Controller
{

    protected CartRepositoryInterface $cartRepository;

    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }



    public function addCart($id)
    {
        $user_id=auth()->id();
         $product=Product::query()->with('courses')->find($id);
        $id=$product->id;
        $title=$product->courses->course_title;
        $picture=$product->courses->picture;
      $cartExists=$this->cartRepository->CheckCartExists($id,$user_id);

      if($cartExists == null){
          if($product['price_discount'] != null)
          {
              $data=[
                  'user_id'=>$user_id,
                  'product_id'=>$id,
                  'name'=>$title,
                  'price'=>$product->price_discount,
                  'attr'=>$picture
              ];


          }else
          {
              $data=[
                  'user_id'=>$user_id,
                  'product_id'=>$id,
                  'name'=>$title,
                  'price'=>$product->price,
                  'attr'=>$picture
              ];
          }

          $cart= $this->cartRepository->create($data);

          return response()->json([
              'message'=>'محصول به سبد خرید شما اضافه شد',
              'data'=>$cart,
          ],201);

      }

      return response()->json('قبلا این محصول را اضافه کردید',422);

  }

    /**
     * @return JsonResponse
     */
    public function getCart(): JsonResponse
    {
        $user_id=auth()->id();
        $cart= $this->cartRepository->CartList($user_id);
        $total= $this->cartRepository->CartTotal($user_id);
       return response()->json([
           'cart'=>$cart,
           'total'=>$total
       ]);
  }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function removeItem($id): JsonResponse
    {
        $ids=explode(",",$id);
        $this->cartRepository->delete($ids);
        return response()->json([
            'message'=>"success",
        ]);
  }


}
