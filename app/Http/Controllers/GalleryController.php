<?php

namespace App\Http\Controllers;

use App\Interfaces\GalleryRepositoryInterface;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{

    protected GalleryRepositoryInterface $galleryRepository;

    public function __construct(GalleryRepositoryInterface $galleryRepository)
    {
        $this->galleryRepository = $galleryRepository;
    }

    public function addGallery(Request $request)
    {
     $request->validate([
           'name'=>'required',
           'picture'=>'required',
           'library_id'=>'required',
       ]);

        $library_ids = array_map(null , $request->library_id);

       $gallery=$this->galleryRepository->create([
           'name'=>$request->name,
           'picture'=>$request->picture,
       ]);

       $gallery->libraries()->attach($library_ids);

       return response()->json([
           'data'=>$gallery,
           'message'=>'added successfully'
       ]);

    }

    public function getGallery()
    {
       $result= $this->galleryRepository->viewGallery();

        return response()->json($result);
    }

    public function editGallery(Request $request, $id)
    {
        $data=[
          'name'=>$request->name,
          'picture'=>$request->picture,

        ];
        $libraries=explode(",",$request->library_id);
        $gallery=$this->galleryRepository->update($id,$data);
        $gallery->libraries()->sync($libraries);

        return response()->json($gallery);
    }

    public function deleteGallery($id)
    {
        $ids=explode(",",$id);
        $this->galleryRepository->delete($ids);
        return response()->json([
            'message'=>'deleted'
        ]);
    }
}
