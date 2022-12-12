<?php

namespace App\Http\Controllers;

use App\Interfaces\ProgressRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoProgressBarController extends Controller
{
    protected ProgressRepositoryInterface $progressRepository;

    public function __construct(ProgressRepositoryInterface $progressRepository)
    {
        $this->progressRepository = $progressRepository;
    }


    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     *
     * get lesson_id and save current time and duration of video
     */
    public function saveTime(Request $request, $id): JsonResponse
    {
        $request->validate([
            'time' => 'required',
            'duration' => 'required'
        ]);

        $user = auth()->user();
        $user_id=$user->id;

        $video= $this->progressRepository->TakeVideo($id);

        $data=[
          'time'=>$request->time,
          'percentage'=>$request->time*100/$request->duration,
          'duration'=>$request->duration,
            'user_id'=>$user_id,
            'lesson_id'=>$video->id
        ];

        if($data['percentage'] == 100){

            $currentScore= $user->score ?? 0;

            $currentScore += 10;

            $user->forceFill(['score'=>$currentScore])->save();
        }

       $OldProgress= $this->progressRepository->CheckProgressBar($user_id,$video->id);

        if($OldProgress){
            $progressId= $OldProgress->id;

            $this->progressRepository->update($progressId,$data);
        }else{
            $this->progressRepository->create($data);
        }




        return response()->json([
            'message' => $data['percentage'],
            'score'=>$user->score
        ]);//send http response as json back to the ajax call

    }

    /**
     *
     * @param $id
     * @return JsonResponse
     *
     * take lesson_id and show percentage of playing video from user
     */
    public function getTime($id): JsonResponse
    {
        $user = Auth::user();
        if($user === null){
            return response()->json(['message' => 'User not authenticated'], 403);
        }

        
        $video = $this->progressRepository->TakeVideo($id);
        //get the time from saved time when you saved it with this data
        $playbackTime = $this->progressRepository->GetVideoTime($video->id,$user->id);


        if($playbackTime === null){
            //there's no saved time
            $playbackTime = 0;
        }else{
            $playbackTime = $playbackTime->percentage;//use what column you saved the time in.
        }
        return response()->json(['playbackTime' => $playbackTime]);
    }


    /**
     * @param $course_id
     * @return JsonResponse
     */
    public function getFullTimeOfAnCourse($course_id): JsonResponse
    {

        $count= $this->progressRepository->GetLessonsCountOfAnCourse($course_id);

        $whereIsFullCount=$this->progressRepository->WhereProgressIsFull($course_id);

        $result=  $whereIsFullCount*100/$count;
        if($result==100){

            $user=\auth()->user();
          $score =$whereIsFullCount*20;
          $currentScore =$user->score ?? 0;
          $currentScore +=$score;
       $user->forceFill(['score'=>$currentScore])->save();

        }
        return response()->json([
            'courseProgress'=>$result,

        ]);
    }


}
