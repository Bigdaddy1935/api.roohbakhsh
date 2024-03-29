<?php

namespace App\Http\Controllers;

use App\Interfaces\CommentRepositoryInterface;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;




class CommentsController extends Controller
{

    protected CommentRepositoryInterface $commentRepository;

    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function addComment(Request $request): JsonResponse
    {

        $comment = new Comment;
        $comment->body = $request->body;
        $comment->user_id=auth()->id();
        if($request->course_id != null){
            $course=Course::query()->find($request->course_id);
       $course->comments()->save($comment);
        }
        elseif ($request->lesson_id != null){
            $lesson=Lesson::query()->find($request->lesson_id);
            $lesson->comments()->save($comment);
        }
        elseif ($request->article_id != null){
            $article=Article::query()->find($request->article_id);
           $article->comments()->save($comment);
        }elseif ($request->product_id != null){
            $product=Product::query()->find($request->product_id);
          $product->comments()->save($comment);
        }else
        {
            return response()->json('give me fucking id',422);
        }

    return response()->json($comment);

    }

    public function replyComment(Request $request): JsonResponse
    {
        $reply = new Comment();
        $reply->body = $request->body;
        $reply->user_id=auth()->id();
        $reply->parent_id = $request->input('comment_id');


        if($request->course_id != null){
            $course=Course::query()->find($request->input('course_id'));
            $course->comments()->save($reply);
        }
        elseif ($request->lesson_id != null){
            $lesson=Lesson::query()->find($request->input('lesson_id'));
            $lesson->comments()->save($reply);
        }
        elseif ($request->article_id != null){
            $article=Article::query()->find($request->input('article_id'));
            $article->comments()->save($reply);
        }elseif ($request->product_id != null){
            $product=Product::query()->find($request->input('product_id'));
            $product->comments()->save($reply);
        }else
        {
            return response()->json('give me fucking id',422);
        }


        return response()->json($reply);

    }

    public function getComment(): JsonResponse
    {
      $comment=$this->commentRepository->getComments();

        return response()->json($comment);
    }

    public function GetAccepted(): JsonResponse
    {
        $comment=$this->commentRepository->AcceptedComments();

        return response()->json($comment);
    }
    public function GetRejected(): JsonResponse
    {
        $comment=$this->commentRepository->rejectedComments();

        return response()->json($comment);
    }

    public function likeComment($id): JsonResponse
    {
        $comment=$this->commentRepository->find($id);

        $like=auth()->user()->toggleLike($comment);

        if(is_bool($like)){
            return response()->json('deleted');
        }else{
            return response()->json('added',201);
        }

    }

    public function AcceptComment($id): JsonResponse
    {

    $data=
    [
        'status' => 1
    ];
         $comment=  $this->commentRepository->update($id,$data);
       return response()->json($comment);
    }

    public function RejectComment($id): JsonResponse
    {

        $data=
            [
                'status'=>0
            ];
        $comment=$this->commentRepository->update($id,$data);
        return response()->json($comment);
}

    public function removeComment($id): JsonResponse
    {
      $ids=  explode(',',$id);
        $this->commentRepository->delete($ids);

        return response()->json([
            'message'=>'success',
            'comment_id'=>$ids
        ]);

    }

    public function GetCommentsByType(Request $request ,$id)
    {
       $type= $request->type;



     $result= $this->commentRepository->getSpecificComments($id,$type);

     return response()->json($result);
    }

    public function GetAcceptedCommentsByTYpe(Request $request , $id)
    {

        $type= $request->type;



        $result= $this->commentRepository->getSpecificAcceptedComments($id,$type);

        return response()->json($result);
    }





}
