<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/12/4
 * Time: 17:22
 */

namespace SimpleShop\Permission\Repositories;


use Illuminate\Support\Collection;
use \SimpleShop\Permission\Contracts\PermissionRepository as RepositoryInterface;
use SimpleShop\Permission\Contracts\UserContract;
use SimpleShop\Permission\Models\Api;
use SimpleShop\Permission\Models\Menu;
use SimpleShop\Permission\Models\MenuApi;
use SimpleShop\Permission\Models\Role;
use SimpleShop\Permission\Models\RoleMenu;
use SimpleShop\Permission\Models\UserRole;
use App;

class PermissionRepository implements RepositoryInterface
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @param  string    $api
     *
     * @return bool
     */
    public function check($api): bool
    {
        $userImpl = $this->getUserImpl();

        if ($userImpl->getIsUltimate()) {
            return true;
        }

        // 获取userId对应的api列表
        $userRole = UserRole::where('user_id', $this->user->id)->pluck('role_id');
        $roleMenu = RoleMenu::whereIn('role_id', $userRole->all())->pluck('menu_id');
        /** @var Collection $menuApi */
        $menuApi = MenuApi::join('apis', function ($query) {
            $query->on('apis.id', '=', 'menu_api.api_id');
        })->whereIn('menu_api.menu_id', $roleMenu->all())->where('apis.path', $api)->where('apis.enable', 1)->pluck('path');
        
        if ($menuApi->isNotEmpty()) {
            return true;
        }

        return false;
    }

    /**
     * 获取这个用户应该有的menu
     *
     * @return mixed
     */
    public function getMenus()
    {
        $userImpl = $this->getUserImpl();

        if ($userImpl->getIsUltimate()) {
            return Menu::where('show', 1)->get();
        }

        $userRole = UserRole::where('user_id', $this->user->id)->pluck('role_id');
        $roleMenu = RoleMenu::whereIn('role_id', $userRole->all())->pluck('menu_id');
        $menus = Menu::whereIn('id', $roleMenu->all())->where('show', 1)->get();

        return $menus;
    }

    /**
     * @return UserContract
     */
    protected function getUserImpl()
    {
        return App::make(UserContract::class);
    }

    /**
     * 获取设定了的route范围
     *
     * @param string $route
     *
     * @return Collection
     */
    public function getRouteScope($route = null)
    {
        if (! is_null($route)) {
            return Api::where('path', $route)->pluck('path');
        }

        return Api::pluck('path');
    }

    /**
     * @param $userId
     *
     * @return array
     * @throws \Exception
     */
    public function getSelfAndUserRole($userId)
    {
        // 先查自己的权限
        $userRoles = UserRole::where('user_id', $this->user->id)->pluck('role_id');
        $selfRoles = Role::whereIn('id', $userRoles->all())->orderBy('deep')->get();

        // 再去查要操作用户的权限
        $userRoles = UserRole::where('user_id', $userId)->pluck('role_id');
        $roles = Role::whereIn('id', $userRoles->all())->orderBy('deep')->get();



        return [
            'self' => $selfRoles,
            'user' => $roles,
        ];
    }
}