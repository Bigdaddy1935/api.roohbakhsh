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
     $data=  $request->validate([
           'name'=>'required',
           'picture'=>'required',
           'library_id'=>'required',
       ]);

       $libraries_id=explode(',',$request->library_id);

       $gallery=$this->galleryRepository->create([
           'name'=>$request->name,
           'picture'=>$request->picture,
           'desc'=>$request->desc == null ?null : $request->desc,
       ]);



       $gallery->libraries()->attach($libraries_id);

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
            'desc'=>$request->desc,
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
