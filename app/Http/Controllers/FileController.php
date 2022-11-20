<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Repositories\FileRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class FileController extends Controller
{


    protected FileRepository $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * add files to file table
     */
    public function addFile(Request $request): JsonResponse
    {

        $request->validate([
            'title'=>'required|string',
            'link'=>'required',
        ]);

        $data=$request->all();

         $files=   $this->fileRepository->create($data);

        return response()->json($files,201);

}


    /**
     * @return JsonResponse
     *
     * get all files
     *
     */
    public function getFiles(): JsonResponse
    {

        $file=$this->fileRepository->all();

        if(!$file){

           return response()->json([
               'message'=>'نا موفق '
           ]);

        }
return response()->json($file);
}


    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     *
     * update files from input id
     */
    public function updateFile(Request $request ,$id): JsonResponse
    {

        $data=$request->all();
        $file=$this->fileRepository->update($id,$data);

        return response()->json([
           'message'=>'فایل مورد نظر با موفقیت ویرایش شد',
           'file'=>$file,
        ]);



}


    /**
     * @param $id
     * @return JsonResponse
     *
     * delete files from inputs ids
     */
    public function deleteFile($id): JsonResponse
    {

        $ids=explode(",",$id);
        $this->fileRepository->delete($ids);
        return response()->json([
           'message'=>'فایل های مورد نظر با موفقیت حذف شدند',
           'files_id'=>$ids,

        ]);


}

}
