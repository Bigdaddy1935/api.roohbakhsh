<?php

namespace App\Providers;

use App\Interfaces\ArticleRepositoryInterface;
use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\CommentRepositoryInterface;
use App\Interfaces\CourseRepositoryInterface;
use App\Interfaces\InvoiceRepositoryInterface;
use App\Interfaces\LessonRepositoryInterface;
use App\Interfaces\LibraryRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\ProgressRepositoryInterface;
use App\Interfaces\SearchRepositoryInterface;
use App\Interfaces\ShowcaseRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\VoucherRepositoryInterface;
use App\Interfaces\ZarinpalRepositoryInterface;
use App\Repositories\ArticleRepository;
use App\Repositories\CartRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CommentRepository;
use App\Repositories\CourseRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\LessonRepository;
use App\Repositories\LibraryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProgressRepository;
use App\Repositories\SearchRepository;
use App\Repositories\ShowcaseRepository;
use App\Repositories\UserRepository;
use App\Repositories\VoucherRepository;
use App\Repositories\ZarinpalPeymentRepository;
use Illuminate\Support\ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
      //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(UserRepositoryInterface::class,UserRepository::class);
        $this->app->bind(CourseRepositoryInterface::class,CourseRepository::class);
        $this->app->bind(ArticleRepositoryInterface::class,ArticleRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class,CategoryRepository::class);
        $this->app->bind(LessonRepositoryInterface::class,LessonRepository::class);
        $this->app->bind(ProductRepositoryInterface::class,ProductRepository::class);
        $this->app->bind(ProgressRepositoryInterface::class,ProgressRepository::class);
        $this->app->bind(ZarinpalRepositoryInterface::class,ZarinpalPeymentRepository::class);
        $this->app->bind(LibraryRepositoryInterface::class,LibraryRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class,InvoiceRepository::class);
        $this->app->bind(CartRepositoryInterface::class,CartRepository::class);
        $this->app->bind(VoucherRepositoryInterface::class,VoucherRepository::class);
        $this->app->bind(CommentRepositoryInterface::class,CommentRepository::class);
        $this->app->bind(SearchRepositoryInterface::class,SearchRepository::class);
        $this->app->bind(ShowcaseRepositoryInterface::class,ShowcaseRepository::class);
    }
}
