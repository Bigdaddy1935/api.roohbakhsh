<?php

namespace App\Http\Controllers;

use App\Interfaces\InvoiceRepositoryInterface;
use App\Interfaces\VoucherRepositoryInterface;
use App\Models\Cart;
use App\Models\Invoice;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController
{

public array $data=[];
    protected InvoiceRepositoryInterface $InvoiceRepository;
    protected VoucherRepositoryInterface $voucherRepository;


    public function __construct(InvoiceRepositoryInterface $InvoiceRepository , VoucherRepositoryInterface $voucherRepository)
    {


        $this->InvoiceRepository = $InvoiceRepository;

        $this->voucherRepository = $voucherRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addInvoice(Request $request): JsonResponse
    {
        $request->validate([
            'order_id'=>'required',
            'amount'=>'required',
            'ref_id'=>'required',
        ]);


      $ids=  explode(',',$request->order_id);
      $amounts=explode(',',$request->amount);




       $code= $request->code;


       //check if user add discount code in cart shopping page we save code and user in pivot table
       if($code != null){
           $code_exists=  $this->voucherRepository->CodeExists($code);
           $code_id=$code_exists->id;
           $user=auth()->user();
           $user->vouchers()->attach($code_id);
       }


       //save all user products in cart as array to invoice table
        for($i = 0 ; $i < count($ids); $i++){
            $this->data=[
                'user_id'=>auth()->id(),
                'order_id'=>$ids[$i],
                'amount'=>$amounts[$i],
                'ref_id'=>$request->ref_id,
                'mobile'=>$request->mobile,
                'email'=>$request->email,
                'card_pan'=>$request->card_pan,
            ];
            $this->InvoiceRepository->create($this->data);
        }

       $id= auth()->id();
        Cart::query()->where('user_id' ,$id)->delete();

        return response()->json($this->data);

    }


    /**
     * @return JsonResponse
     *
     * get history of users products buy
     */
    public function getInvoice()
    {

       $user= auth()->user();
      $user_invoice= Invoice::query()->where('user_id',$user->id)->with('products:id,title')->get();

       return response()->json($user_invoice);
    }

    public function TotalAmount()
    {
       $res= Invoice::query()->sum('amount');

       return response()->json([
           'TotalAmount'=>$res
       ]);
    }

    public function TotalSell()
    {
        $res=Invoice::query()->count('order_id');

        return response()->json([
            'TotalSell'=>$res
        ]);

    }

}