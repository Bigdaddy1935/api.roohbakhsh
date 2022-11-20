<?php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use App\Repositories\TutorialRepository;
use Illuminate\Http\Request;

class TutorialController extends Controller
{

    protected TutorialRepository $tutorialRepository;

    public function __construct(TutorialRepository $tutorialRepository)
    {
        $this->tutorialRepository = $tutorialRepository;
    }

    public function addTutorial(Request $request)
    {
        $request->validate([
            'title'=>'required',
            'video_url'=>'required',
        ]);
        $data=$request->all();
        $this->tutorialRepository->create($data);

        return response()->json($data,201);
    }

    public function updateTutorial(Request $request,$id)
    {
        $data=$request->all();

        $this->tutorialRepository->update($id,$data);

        return response()->json($data);
    }

    public function deleteTutorial($id)
    {

        $ids=explode(',',$id);
        $this->tutorialRepository->delete($ids);

        return response()->json([
            'message'=>'ویدیو های مورد نظر با موفقیت پاک شد',
            'ids'=>$ids
        ]);

    }

    public function showTutorial()
    {
        return Tutorial::all();
    }

}
