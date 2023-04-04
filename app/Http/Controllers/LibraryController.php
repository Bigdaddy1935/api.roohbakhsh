<?php

namespace App\Http\Controllers;

use App\Interfaces\LibraryRepositoryInterface;
use App\Models\Lesson;
use App\Models\Library;
use App\QueryFilters\Categories;
use App\QueryFilters\Course_id;
use App\QueryFilters\Sort;
use App\QueryFilters\Teacher;
use App\QueryFilters\Title;
use App\QueryFilters\Type;
use App\QueryFilters\User_id;
use App\QueryFilters\Visibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Validation\ValidationException;

class LibraryController extends Controller
{

    protected $url;
    private LibraryRepositoryInterface $libraryRepository;


    public function __construct(LibraryRepositoryInterface $libraryRepository)
    {

        $this->libraryRepository = $libraryRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     *
     *
     * validate and save input into library table
     */
    public function addLibrary(Request $request): JsonResponse
    {

        $this->validate($request,[

            'title'=>'required',
            'size'=>'required',
            'picture'=>'required',
            'type'=>'required',
        ]);


     $url= $this->libraryRepository->upload($request->file('picture'));

        $data=[
            'title'=>$request->title,
             'size'=>$request->size,
             'picture'=>$url,
             'type'=>$request->type,
            'desc'=>$request->desc == null ? null : $request->desc,
            ];
        $library = $this->libraryRepository->create($data);
        return response()->json($library,201);
    }


    /**
     * @return JsonResponse
     *
     *
     * get all files in library
     */
    public function getLibrary(): JsonResponse
    {
       $library = $this->libraryRepository->all();
       if(!$library){
           return response()->json([
               'message'=>'هیچ کتابخانه ای وجود ندارد'
           ]);
       }
    return response()->json($library);
    }



    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     *
     * update library (id) request inputs
     */
    public function updateLibrary(Request $request , $id): JsonResponse
    {


        $file=$request->file('picture');
        if(!empty($file)){
         $this->libraryRepository->upload($file);
        }

        $data=$request->all();

        $library=$this->libraryRepository->update($id,$data);


        return response()->json([
            'message'=>'کتابخانه ی شما با موفقیت ویرایش شد',
            'library'=>$library,
        ]);

    }


    /**
     * @param $id
     * @return JsonResponse
     *
     *
     * destroy libraries as ids we get from param
     */
    public function deleteLibrary($id): JsonResponse
    {
        $ids=explode(",",$id);
        $this->libraryRepository->delete($ids);

        return response()->json([
            'message'=>'کتابخانه های مورد نظر با موفقیت حذف شدند',
            'libraries_id'=>$ids,
        ]);

    }
    public function index(): JsonResponse
    {

        $library=app(Pipeline::class)->send(Library::query())->through([
            Title::class,
            Type::class,
            Sort::class,

        ])
            ->thenReturn()
            ->orderBy('id','DESC')
            ->paginate(10);


        return response()->json($library);
    }
}
