<?php

namespace App\Repositories;

use App\Interfaces\ArticleRepositoryInterface;
use App\Models\Article;

class ArticleRepository extends Repository implements ArticleRepositoryInterface
{

    public function model()
    {
        return Article::class;
    }

    public function GetArticlesData()
    {
        $user= auth('sanctum')->id();

          return   Article::query()
            ->join('users','users.id','=','articles.user_id')
            ->select('articles.*','users.fullname')
            ->with(['tagged','categories'])
            ->withAggregate('visits','score')
             ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
          $q->where('user_id',$user);
             }])
              ->with('relatedArticles')
              ->with('relatedLessons',function ($q){
                  $q->join('lessons','lessons.id','=','lesson_related_for_articles.lesson_id')->select('lessons.title','lesson_related_for_articles.*');
              })
              ->orderBy('id','DESC')
            ->paginate(10);
    }

    public function GetSpecificArticle($id)
    {
     return   Article::query()
            ->join('users','users.id','=','articles.user_id')
            ->select('articles.*','users.fullname')
            ->with('tagged')
            ->with('categories')
            ->with('relatedArticles')
            ->with('relatedLessons',function ($q){
                $q->join('lessons','lessons.id','=','lesson_related_for_articles.lesson_id')->select('lessons.title','lesson_related_for_articles.*');
            })
            ->findOrFail($id);
    }


    public function ArticlesCount()
    {
        return Article::query()->get()->count();
    }


    public function ArticlesFromTag($tags, $user)
    {
        return Article::withAnyTag($tags)
            ->join('users','users.id','=','articles.user_id')
            ->select('articles.*','users.fullname')
            ->with(['tagged','categories'])
            ->withAggregate('visits','score')
            ->withExists(['bookmarkableBookmarks as bookmark'=>function($q) use ($user){
                $q->where('user_id',$user);
            }])
            ->with('relatedArticles')
            ->with('relatedLessons',function ($q){
                $q->join('lessons','lessons.id','=','lesson_related_for_articles.lesson_id')->select('lessons.title','lesson_related_for_articles.*');
            })
            ->orderBy('id','DESC')
            ->paginate(10);
    }

    public function ArticleList()
    {
       return Article::query()->orderBy('title')->get();
    }
}