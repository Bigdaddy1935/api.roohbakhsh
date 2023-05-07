<?php

namespace App\Http\Controllers;

use App\Interfaces\ShowcaseRepositoryInterface;
use App\Models\Article;
use App\Models\Cart;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Lesson;
use App\Models\Product;
use App\Models\Showcase;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowcaseController extends Controller
{

    protected ShowcaseRepositoryInterface $showcaseRepository;

    public function __construct(ShowcaseRepositoryInterface $showcaseRepository)
    {

        $this->showcaseRepository = $showcaseRepository;
    }
    public function addShowcase(Request $request)
    {
       $showcase= new Showcase;
       $showcase->picture=$request->picture;
       $showcase->url=$request->url;
       $showcase->ends_at=$request->ends_at;



        if($request->course_id != null){
            $course=Course::query()->find($request->course_id);

            if($request->ends_at != null){
                $showcase->expiresAt(Carbon::now()->addHours($request->ends_at));
            }
            $showcase->expiresAt(Carbon::now()->addMonths(6));
            $course->showcases()->save($showcase);
        }

        elseif ($request->lesson_id != null){
            $lesson=Lesson::query()->find($request->lesson_id);
            if($request->ends_at != null){
                $showcase->expiresAt(Carbon::now()->addHours($request->ends_at));
            }
            $showcase->expiresAt(Carbon::now()->addMonths(6));
            $lesson->showcases()->save($showcase);

        }
        elseif ($request->article_id != null){
            $article=Article::query()->find($request->article_id);
            if($request->ends_at != null){
                $showcase->expiresAt(Carbon::now()->addHours($request->ends_at));
            }
            $showcase->expiresAt(Carbon::now()->addMonths(6));
            $article->showcases()->save($showcase);

        }elseif ($request->product_id != null){
            $product=Product::query()->find($request->product_id);
            if($request->ends_at != null){
                $showcase->expiresAt(Carbon::now()->addHours($request->ends_at));
            }
            $showcase->expiresAt(Carbon::now()->addMonths(6));
            $product->showcases()->save($showcase);
        }else {

            if($request->ends_at != null){
                $showcase->expiresAt(Carbon::now()->addHours($request->ends_at));
            }
            $showcase->expiresAt(Carbon::now()->addMonths(6));
            Showcase::query()->create([
                'picture'=>$request->picture,
                'url'=>$request->url,
                'ends_at'=>$showcase->ends_at
            ]);

            return response()->json([
                'message'=>'showcase url added successfully'
            ]);
        }

        return response()->json([
            'message'=>'showcase added successfully'
        ]);

    }

    public function getShowcase()
    {

        $NotExpire= $this->showcaseRepository->getNotExpired();

        $expire= $this->showcaseRepository->getExpired();

        return response()->json([
            'NotExpired'=>$NotExpire,
            'Expired'=>$expire,
        ]);
    }

    public function deleteShowcase($id): JsonResponse
    {
        $ids=explode(",",$id);
      $res= Showcase::destroy($ids);

        return response()->json([
            'message'=>'ویترین مورد نظر با موفقیت حذف شد',
            'showcase_id'=>$res,
        ]);

    }

    public function updateShowcase(Request $request,$id)
    {

        $data=[
            'model_type'=>$request->model_type,
            'model_id'=>$request->model_id,
            'picture'=>$request->picture,
            'url'=>$request->url,
            'ends_at'=>$request->ends_at,
        ];
        $showcase=Showcase::query()->where('id',$id)->update($data);

        return response()->json($data);

    }
}
