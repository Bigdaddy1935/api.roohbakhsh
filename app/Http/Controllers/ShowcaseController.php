<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Product;
use App\Models\Showcase;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowcaseController extends Controller
{
    public function addShowcase(Request $request)
    {
       $showcase= new Showcase;
       $showcase->picture=$request->picture;
       $showcase->url=$request->url;
       $showcase->ends_at=$request->ends_at;

        if($request->course_id != null){
            $course=Course::query()->find($request->course_id);
            $showcase->expiresAt(Carbon::now()->addHours($request->ends_at));
            $course->showcases()->save($showcase);

        }
        elseif($request->media_id != null){
            $showcase->expiresAt(Carbon::now()->addHours($request->ends_at));
            Showcase::query()->create([
                'picture'=>$request->picture,
                'model_type'=>'App\Models\Media',
                'model_id'=>$request->media_id,
                'ends_at'=>$showcase->ends_at,
            ]);

        }
        elseif ($request->lesson_id != null){
            $lesson=Lesson::query()->find($request->lesson_id);
            $showcase->expiresAt(Carbon::now()->addHours($request->ends_at));
            $lesson->showcases()->save($showcase);

        }
        elseif ($request->article_id != null){
            $article=Article::query()->find($request->article_id);
            $showcase->expiresAt(Carbon::now()->addHours($request->ends_at));
            $article->showcases()->save($showcase);

        }elseif ($request->product_id != null){
            $product=Product::query()->find($request->product_id);
            $showcase->expiresAt(Carbon::now()->addHours($request->ends_at));
            $product->showcases()->save($showcase);
        }else {

            $showcase->expiresAt(Carbon::now()->addHours($request->ends_at));
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
        $notexpire=Showcase::query()->get();

        $expire=Showcase::query()->onlyExpired()->get();

        return response()->json([
            'NotExpired'=>$notexpire,
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
