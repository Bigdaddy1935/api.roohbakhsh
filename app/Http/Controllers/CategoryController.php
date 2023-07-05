<?php

namespace App\Http\Controllers;

use App\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $result = [];
    protected CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     *
     *
     * validate and save category in categories table
     */

    public function addCategory(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name',
            'slug'=>'required|unique:categories,slug',
            'picture'=>'required'
        ]);

      $data=[
          'name'=>$request->name,
          'slug'=>$request->slug,
          'picture'=>$request->picture,
          'description'=>$request->description,
          'parent_id'=>$request->parent_id == null ?null :$request->parent_id,
      ];

      $category= $this->categoryRepository->create($data);

        return response()->json($category);
    }


    /**
     *
     * @return JsonResponse
     *
     *
     * get categories with sub category
     */
    public function getCategory(): JsonResponse
    {

             $categories= $this->categoryRepository->GetCatWithSub();
        if(!$categories) {
            return response()->json( [
                'message' => 'دسته بندی ثبت نشده'
            ]);
        }
            return response()->json($categories);


}


    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     *
     * update input category id requests
     */
    public function updateCategory(Request $request ,$id): JsonResponse
    {

          $data=[
            'name'=>$request->name,
            'slug'=>$request->slug,
            'picture'=>$request->picture,
            'description'=>$request->description,
            'parent_id'=>$request->parent_id == null ?null :$request->parent_id,
        ];
        $category   = $this->categoryRepository->update($id,$data);
        return response()->json([
            'message'=>'دوره مورد نظر با موفقیت ویرایش شد',
            'category_id'=>$id,
            'category'=>$category
        ]);
    }


    /**
     * @param $id
     * @return JsonResponse
     *
     * delete inputs ids from category table
     */
    public function deleteCategory($id): JsonResponse
    {

        $ids=explode(",",$id);
        $this->categoryRepository->delete($ids);
        return response()->json([
            'message'=>"دسته بندی مورد نظر با موفقیت حذف شد",
            'id'=>$ids
        ]);

    }




    public function getAll(): JsonResponse
    {
        $cat = $this->categoryRepository->all();

        return response()->json($cat);

    }


    /**
     * @param $id
     * @return JsonResponse
     *
     *
     * get courses  at theirs category
     */
    public function get_course_cat($id): JsonResponse
    {

    $cat=$this->categoryRepository->Get_Course_With_Their_Cat($id);

        return response()->json($cat);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function get_lesson_cat($id): JsonResponse
    {



        $cat=$this->categoryRepository->Get_Lesson_With_Their_Cat($id);

        return response()->json($cat);
    }

    public function get_podcast_cat($id)
    {
        $cat=$this->categoryRepository->Get_Podcast_With_Their_Cat($id);

        return response()->json($cat);
    }

    public function get_mahdyar_question_cat($id): JsonResponse
    {
        $cat=$this->categoryRepository->Get_Mahdyar_Question_With_Their_Cat($id);


        return response()->json($cat);
    }

    public function get_article_cat($id): JsonResponse
    {


        $cat=$this->categoryRepository->Get_Article_With_Their_Cat($id);

        return response()->json($cat);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function get_product_cat($id): JsonResponse
    {

        $cat=$this->categoryRepository->Get_Product_With_Their_Cat($id);


            return response()->json($cat);

    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function get_club_cat($id): JsonResponse
    {
        $cat=$this->categoryRepository->Get_Club_With_Their_Cat($id);

        return response()->json($cat);
    }
}