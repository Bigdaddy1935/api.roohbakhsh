<?php

namespace App\Http\Controllers;

use App\Interfaces\VoucherRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherController extends Controller
{

    protected VoucherRepositoryInterface $voucherRepository;

    public function __construct(VoucherRepositoryInterface $voucherRepository)
    {
        $this->voucherRepository = $voucherRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addVoucher(Request $request): JsonResponse
    {
        $request->validate([
            'code'=>'required|unique:vouchers,code',
            'discount_amount'=>'required'
        ]);
        $data=$request->all();
     $voucher= $this->voucherRepository->create($data);

        return response()->json($voucher);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function useVoucher(Request $request): JsonResponse
    {
        $request->validate([
            'discount_code'=>'required',
        ]);

        $code=$request->discount_code;

       $code_exists=$this->voucherRepository->CodeExists($code);

       if($code_exists){
           $id=auth()->id();
           $codeUsed=$this->voucherRepository->CodeUsed($id);

         if($codeUsed != null){
             return response()->json('قبلا از این کد استفاده کردید',422);
         }else
         {
             $totalPrice= $this->voucherRepository->GetTotalPriceOfCart($id);
             $discount= $code_exists->discount_amount;
             $discounted=($discount*$totalPrice)/100;
             $finalPrice= $totalPrice-$discounted;


             return response()->json([
                'message'=> 'کد تخفیف با موفقیت اعمال شد',
                'newTotal'=> $finalPrice
             ]);

         }


       }


       return response()->json('کد تخفیف اشتباه است',422);
    }

    public function getVoucher()
    {
       $vouches= $this->voucherRepository->all();

       return response()->json($vouches);
    }

    public function editVoucher( $id, Request $request)
    {
       $fields= $request->all();

       $new=$this->voucherRepository->update($id,$fields);

       return response()->json($new);
    }

    public function deleteVoucher($id)
    {
        $ids=explode(",",$id);

        $this->voucherRepository->delete($ids);

        return response()->json([
           'message'=>"کد تخفیف با موفقیت حذف شد"
        ]);
    }
}
