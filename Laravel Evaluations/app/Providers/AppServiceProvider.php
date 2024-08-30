<?php

namespace App\Providers;

use App\Models\Setting;
use App\Repositories\LogInterface;
use App\Repositories\LogRepository;
use App\Repositories\ModuleInterface;
use App\Repositories\ModuleRepository;
use App\Repositories\PermissionInterface;
use App\Repositories\PermissionRepository;
use App\Repositories\ProjectInterface;
use App\Repositories\ProjectParticipantsInterface;
use App\Repositories\ProjectRepository;
use App\Repositories\ProjectsParticipantsRepository;
use App\Repositories\ProjectTypeInterface;
use App\Repositories\ProjectTypeRepository;
use App\Repositories\RoleRepository;
use App\Repositories\RoleInterface;
use App\Services\EmailServiceInterface;
use App\Services\ExcelService;
use App\Services\ExcelServiceInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\UserInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRoleInterface;
use App\Repositories\UserRoleRepository;
use App\Services\EmailService;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    public function boot()
    {
        Schema::defaultStringLength(191);

        view()->composer('tests.completed', function($view){
            view()->share('settingsArray', Setting::all()->pluck('value', 'key')->toArray());
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(UserInterface::class, UserRepository::class);
        $this->app->singleton(RoleInterface::class, RoleRepository::class);
        $this->app->singleton(PermissionInterface::class, PermissionRepository::class);
        $this->app->singleton(ModuleInterface::class, ModuleRepository::class);
        $this->app->singleton(ProjectInterface::class, ProjectRepository::class);
        $this->app->singleton(ProjectTypeInterface::class, ProjectTypeRepository::class);
        $this->app->singleton(ProjectParticipantsInterface::class, ProjectsParticipantsRepository::class);
        $this->app->singleton(UserRoleInterface::class, UserRoleRepository::class);
        $this->app->singleton(LogInterface::class, LogRepository::class);
        $this->app->singleton(EmailServiceInterface::class, EmailService::class);
        $this->app->singleton(ExcelServiceInterface::class, ExcelService::class);
    }
}
