<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
use App\Policies\UserPolicy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(TaskRepositoryInterface::class,    TaskRepository::class);
        $this->app->bind(UserRepositoryInterface::class,    UserRepository::class);
    }

    public function boot(): void
    {
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Task::class,    TaskPolicy::class);
        Gate::policy(User::class,    UserPolicy::class);
    }
}
