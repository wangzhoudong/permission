<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/20
 * Time: 11:24
 */

namespace SimpleShop\Permission;


use Illuminate\Support\ServiceProvider;
use SimpleShop\Permission\Contracts\MenuRepository;
use SimpleShop\Permission\Contracts\PermissionRepository;
use SimpleShop\Permission\Contracts\RoleRepository;
use SimpleShop\Permission\Repositories\MenuRepository as MenuRepositoryImpl;
use SimpleShop\Permission\Repositories\RoleRepository as RoleRepositoryImpl;
use SimpleShop\Permission\Contracts\UserContract;
use SimpleShop\Permission\User as UserImpl;
use SimpleShop\Permission\Repositories\PermissionRepository as PermissionImpl;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * 服务提供者是否延迟加载
     *
     * @var bool
     */
    protected $defer = true;

    public function boot()
    {
        $this->loadMigrationsFrom(dirname(__DIR__) . '/database/migrations');
    }

    public function register()
    {
        $this->app->singleton(MenuRepository::class, MenuRepositoryImpl::class);
        $this->app->singleton(RoleRepository::class, RoleRepositoryImpl::class);
        $this->app->singleton(UserContract::class, UserImpl::class);
        $this->app->singleton(PermissionRepository::class, PermissionImpl::class);
    }

    /**
     * 获取由提供者提供的服务.
     *
     * @return array
     */
    public function provides()
    {
        return [
            MenuRepository::class,
            RoleRepository::class,
            UserContract::class,
        ];
    }
}