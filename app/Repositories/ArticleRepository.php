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
            ->with('related')
            ->with('lesson')
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
            ->orderBy('id','DESC')
            ->paginate(10);
    }

    public function ArticleList()
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
            ->orderBy('id','DESC')
            ->get();
    }
}