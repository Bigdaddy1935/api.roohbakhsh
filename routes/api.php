<?php

use App\Http\Controllers\AppNotificationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TutorialController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoProgressBarController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\ZarinpalPayment;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//CMS ROUTES
Route::middleware('auth:sanctum')->prefix('courses')->controller(CourseController::class)->group(function () {
    Route::get('get','getCourses');
    Route::get('get/counts','CoursesCounts');
    Route::get('get/{id}','get_course_by_id');
    Route::get('list','list');
    Route::post('add', 'addCourse');
    Route::get('/','index');
    Route::post('update/{id}', 'updateCourse');
    Route::delete('delete/{id}','deleteCourse');
});


Route::middleware('auth:sanctum')->controller(HomeController::class)->group(function (){
    Route::post('/notification','SendNotify');
    Route::get('/notification/get','getNotify');
    Route::delete('/notification/delete/{id}','delNotify');
});


Route::middleware('auth:sanctum')->prefix('lessons')->controller(LessonController::class)->group(function (){
    Route::post('add','addLesson');
    Route::get('get','getLessons');
    Route::get('get/counts','Lcount');
    Route::get('get/{id}','get_lesson_by_id');
    Route::get('/','index');
    Route::get('take','takeLesson');
    Route::delete('delete/{id}','deleteLesson');
    Route::post('update/{id}', 'updateLesson');

});
Route::middleware('auth:sanctum')->prefix('categories')->controller(CategoryController::class)->group(function (){
    Route::post('add','addCategory');
    Route::get('get','getCategory');
    Route::get('get-all','getAll');
    Route::post('update/{id}','updateCategory');
    Route::delete('delete/{id}','deleteCategory');
});
Route::middleware('auth:sanctum')->prefix('articles')->controller(ArticleController::class)->group(function (){
    Route::post('add','addArticle');
    Route::get('/','index');
    Route::post('like','LikePost');
    Route::post('bookmark','bookmarkPost');
    Route::post('update/{id}','updateArticle');
    Route::get('get','getArticles');
    Route::get('get/counts','ArticlesCount');
    Route::get('get/{id}','get_article_by_id');
    Route::delete('delete/{id}','deleteArticle');
});
Route::prefix('users')->controller(UserController::class)->group(function(){
    Route::post('register','register');
    Route::middleware('auth:sanctum')->get('get','getUsers');
    Route::middleware('auth:sanctum')->get('get/counts','UsersCount');
    Route::middleware('auth:sanctum')->get('/','index');
    Route::post('login','login_cms');
    Route::middleware('auth:sanctum')->post('update/{id}','updateUsers');
    Route::middleware('auth:sanctum')->delete('delete/{id}','deleteUsers');
    Route::middleware('auth:sanctum')->get('logout','logout');
});
Route::middleware('auth:sanctum')->prefix('products')->controller(ProductController::class)->group(function (){
    Route::post('add','addProduct');
    Route::get('get','getProducts');
    Route::get('get/counts','productCount');
    Route::get('get/{id}','get_product_by_id');
    Route::get('list','list');
    Route::get('/','index');
    Route::get('get/related/{id}','relatedProducts');
    Route::post('update/{id}','updateProducts');
    Route::delete('delete/{id}','deleteProducts');
});
Route::middleware('auth:sanctum')->prefix('libraries')->controller(LibraryController::class)->group(function (){
    Route::middleware('auth:sanctum')->get('/','index');
    Route::post('add','addLibrary');
    Route::get('get','getLibrary');
    Route::post('update/{id}','updateLibrary');
    Route::delete('delete/{id}','deleteLibrary');
});
Route::middleware('auth:sanctum')->prefix('files')->controller(FileController::class)->group(function (){

    Route::post('add','addFile');
    Route::get('get','getFiles');
    Route::post('update/{id}','updateFile');
    Route::delete('delete/{id}','deleteFile');

});

Route::middleware('auth:sanctum')->prefix('discount')->controller(VoucherController::class)->group(function (){
    Route::post('add','addVoucher');
    Route::get('get','getVoucher');
    Route::post('update/{id}','editVoucher');
    Route::delete('delete/{id}','deleteVoucher');
});
Route::prefix('tutorial')->controller(TutorialController::class)->group(function (){
    Route::middleware('auth:sanctum')->post('add','addTutorial');
    Route::middleware('auth:sanctum')->post('update/{id}','updateTutorial');
    Route::middleware('auth:sanctum')->delete('delete/{id}','deleteTutorial');
    Route::middleware('auth:sanctum')->get('get','showTutorial');
});

Route::prefix('invoices')->controller(InvoiceController::class)->group(function (){
   Route::get('total/amount','TotalAmount');
   Route::get('total/sell','TotalSell');
});




//APP ROUTES
Route::middleware('auth:sanctum')->prefix('app')->controller(AppNotificationController::class)->group(function (){
    Route::post('store','storeToken');
});

Route::prefix('app')->controller(ZarinpalPayment::class)->group(function (){
    Route::post('/zarinpal','ZarinpalRequest');
    Route::get('/zarinpal/verify','ZarinpalVerify')->name('verify');
    Route::post('invoice','invoicepage');
    Route::get('invoice/verify','verifyInvoice');
});
Route::prefix('app')->controller(HomeController::class)->group(function (){
    Route::post('/search','search')->name('search');
    Route::get('/tutorial','showTutorial');
    Route::post('/notification','SendNotify');
});
Route::prefix('app/articles')->controller(ArticleController::class)->group(function (){
    Route::get('get','getArticles');
    Route::get('/','index');
    Route::middleware('auth:sanctum')->get('like/{id}','likeArticle');
    Route::middleware('auth:sanctum')->get('bookmark/{id}','bookmarkArticle');
    Route::get('get/{id}','get_article_by_id')->name('article.get');
    Route::post('from/tags','ArticlesTags');
});
Route::prefix('app/courses')->controller(CourseController::class)->group(function (){
    Route::get('get','getCourses');
    Route::get('/','index');
    Route::get('get/course/count','getCourseLessonsCount');
    Route::middleware('auth:sanctum')->get('like/{id}','likeCourse');
    Route::middleware('auth:sanctum')->get('bookmark/{id}','bookmarkCourse');
    Route::middleware('auth:sanctum')->get('get/{id}','get_course_by_id')->name('course.get');
});
Route::prefix('app/lessons')->controller(LessonController::class)->group(function (){
    Route::get('/','index');
    Route::get('get/by_course_id/{id}','getCourseLessons');
    Route::middleware('auth:sanctum')->get('like/{id}','likeLesson');
    Route::middleware('auth:sanctum')->get('bookmark/{id}','bookmarkLesson');
    Route::middleware('auth:sanctum')->get('get/{id}','get_lesson_by_id')->name('lessons.get');

});

Route::middleware('auth:sanctum')->prefix('app/progress')->controller(VideoProgressBarController::class)->group(function (){
    Route::post('save/time/{id}', 'saveTime')->name('video.saveTime');
    Route::get('get/time/{id}', 'getTime')->name('video.getTime');
    Route::get('get/fulltime/{id}', 'getFullTimeOfAnCourse');
});


Route::prefix('app/categories')->controller(CategoryController::class)->group(function (){
    Route::get('get','getCategory');
    Route::get('get/all','getAll');
    Route::get('get/courses/{id}','get_course_cat');
    Route::get('get/lessons/{id}','get_lesson_cat');
    Route::get('get/articles/{id}','get_article_cat');
    Route::get('get/products/{id}','get_product_cat');

});
Route::middleware('auth:sanctum')->prefix('app/users')->controller(UserController::class)->group(function (){
    Route::withoutMiddleware('auth:sanctum')->post('add/phone','addPhone');
    Route::withoutMiddleware('auth:sanctum')->post('add/phone/verify','verifyPhone');
    Route::withoutMiddleware('auth:sanctum')->post('login','login_app');
    Route::withoutMiddleware('auth:sanctum')->post('register','register');
    Route::withoutMiddleware('auth:sanctum')->post('logout','logout');
    Route::withoutMiddleware('auth:sanctum')->post('forget/password','resetPassword');
    Route::withoutMiddleware('auth:sanctum')->post('/new-password','newPass');
    Route::withoutMiddleware('auth:sanctum')->get('teachers','getTeachers');
    Route::get('auth','auth');
    Route::post('update/{id}','updateUsers');
    Route::post('deposit','deposit');
    Route::post('withdraw','useBalance');
    Route::get('likes','userLikes');
    Route::get('bookmarks','userBookmarks');
    Route::get('lesson/score','addScoreForLessonsProgress');
    Route::get('lessons_learned','lessonsLearned');
    Route::get('courses/see','CourseProgress');
    Route::get('courses/full','CourseProgressFull');
    Route::get('lessons_see','LessonsSee');
    Route::get('purchased_products_count','PurchasedProductsCount');
    Route::get('purchased_products','PurchasedProducts');
    Route::get('course/score','addScoreForCoursesProgress');
    Route::get('score/deposit','userScoreDeposit');
    Route::get('deposit/history','getDepositHistory');


});
Route::prefix('app/products')->controller(ProductController::class)->group(function (){
    Route::get('get','getProducts');
    Route::get('/','index');
    Route::get('news','newProduct');
    Route::get('get/related/{id}','relatedProducts');
    Route::get('get/{id}','get_product_by_id')->name('product.get');
    Route::middleware('auth:sanctum')->get('like/{id}','likeProduct');
    Route::middleware('auth:sanctum')->get('bookmark/{id}','bookmarkProduct');
});

Route::middleware('auth:sanctum')->prefix('app/invoices')->controller(InvoiceController::class)->group(function (){
    Route::post('add','addInvoice');
    Route::get('get','getInvoice');

});


Route::middleware('auth:sanctum')->prefix('app/cart')->controller(CartController::class)->group(function (){
    Route::post('add/{id}','addCart');
    Route::get('get','getCart');
    Route::delete('delete/{id}','removeItem');
});

Route::prefix('app/discount')->controller(VoucherController::class)->group(function (){
    Route::middleware('auth:sanctum')->post('add','addVoucher');
    Route::middleware('auth:sanctum')->post('use','useVoucher');
});

Route::prefix('app/comment')->controller(CommentsController::class)->group(function (){
    Route::middleware('auth:sanctum')->post('add','addComment');
    Route::middleware('auth:sanctum')->post('add/reply','replyComment');
    Route::middleware('auth:sanctum')->post('like/{id}','likeComment');
    Route::middleware('auth:sanctum')->post('accept/{id}','AcceptComment');
    Route::middleware('auth:sanctum')->post('remove/{id}','removeComment');
    Route::middleware('auth:sanctum')->post('reject/{id}','RejectComment');
    Route::get('get/{id}','getComment');
});










//SITE ROUTES
Route::middleware('auth:sanctum')->prefix('tickets')->controller(TicketsController::class)->group(function (){
    Route::post('add','addTicket');
    Route::post('add/department','addDepartment');
    Route::get('get','getTickets');
    // delete and update
});
