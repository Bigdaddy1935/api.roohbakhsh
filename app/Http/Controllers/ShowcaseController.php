<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Product;
use App\Models\Showcase;
use Carbon\Carbon;
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
            $course->showcases()->save($showcase);
            $showcase->expiresAt(Carbon::now()->addHour($request->expire));
        }
        elseif ($request->lesson_id != null){
            $lesson=Lesson::query()->find($request->lesson_id);
            $showcase->expiresAt(Carbon::now()->addHour($request->expire));
            $lesson->showcases()->save($showcase);

        }
        elseif ($request->article_id != null){
            $article=Article::query()->find($request->article_id);
            $showcase->expiresAt(Carbon::now()->addHour($request->expire));
            $article->showcases()->save($showcase);

        }elseif ($request->product_id != null){
            $product=Product::query()->find($request->product_id);
            $showcase->expiresAt(Carbon::now()->addHour($request->expire));
            $product->showcases()->save($showcase);
        }else {

            $showcase->expiresAt(Carbon::now()->addHour($request->expire));
            Showcase::query()->create([
                'picture'=>$request->picture,
                'url'=>$request->url,
                'ends_at'=>$request->ends_at,
            ]);

            return response()->json([
                'message'=>'showcase url added successfully'
            ]);
        }

        return response()->json([
            'message'=>'showcase added successfully'
        ]);

    }
}
