<?php

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Article;
use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Product;

class CategoryRepository extends Repository implements CategoryRepositoryInterface
{

    public function model()
    {
        return Category::class;
    }

    public function GetCatWithSub()
    {
      return  Category::query()->whereNull('parent_id')
            ->with(['children'])
            ->get();
    }

    public function Get_Course_With_Their_Cat($id)
    {
        $user= auth('sanctum')->id();
      return  Course::query()->where('type','=','course')->withWhereHas('categories',function ($q) use ($id){
            $q->where('id',$id);
        })
            ->join('users','users.id','=','courses.course_user_id')
            ->select('courses.*','users.fullname')
            ->withCount('lessons')
            ->withAggregate('visits','score')
             ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
              $q->where('user_id',$user);
             }])
          ->orderBy('id','DESC')
            ->paginate(10);
    }

    public function Get_Lesson_With_Their_Cat($id)
    {
        $user= auth('sanctum')->id();
       return Lesson::query()->withWhereHas('categories',function ($q) use ($id){
            $q->where('id',$id);
        })
            ->join('users','users.id','=','lessons.user_id')
            ->select('lessons.*','users.fullname')
            ->with('categories')
           ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
             $q->where('user_id',$user);
             }])
           ->orderBy('id','DESC')
            ->paginate(10);
    }



    public function Get_Article_With_Their_Cat($id)
    {
        $user= auth('sanctum')->id();
       return Article::query()->withWhereHas('categories',function ($q) use ($id){
            $q->where('id',$id);
        })
            ->join('users','users.id','=','articles.user_id')
            ->select('articles.*','users.fullname')
            ->with('tagged')
            ->with('categories')
           ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
            $q->where('user_id',$user);
            }])
           ->orderBy('id','DESC')
            ->paginate(10);
    }

    public function Get_Product_With_Their_Cat($id)
    {

        $user= auth('sanctum')->id();
        return     Product::query()->withWhereHas('categories',function ($q) use ($id){
            $q->where('id',$id);
        })
            ->with(['courses'=>function ($q) {
                $q->withCount('lessons');
            }])
            ->with('categories')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->orderBy('id','DESC')
            ->paginate(10);
    }


    public function Get_Podcast_With_Their_Cat($id)
    {
        $user= auth('sanctum')->id();
        return Lesson::query()->where('formats','=','sound')->withWhereHas('categories',function ($q) use ($id){
            $q->where('id',$id);
        })
            ->join('users','users.id','=','lessons.user_id')
            ->select('lessons.*','users.fullname')
            ->with('categories')
            ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->orderBy('id','DESC')
            ->paginate(10);
    }
}