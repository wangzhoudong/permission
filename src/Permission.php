<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/12/4
 * Time: 17:01
 */

namespace SimpleShop\Permission;


use SimpleShop\Permission\Repositories\PermissionRepository;
use App;

class Permission
{
    /**
     * @var PermissionRepository
     */
    private $repo;

    private $user;

    public function __construct($user)
    {
        $this->user = $user;
        $this->repo = App::makeWith(PermissionRepository::class, ['user' => $user]);
    }

    /**
     * 获取是否有权限
     * 通过userId检查
     *
     * @param string $api
     *
     * @return bool
     */
    public function getPermissionByUserId($api)
    {
        return $this->repo->check($api);
    }

    /**
     * 获取当前用户对应的menus
     *
     * @return mixed
     */
    public function getMenus()
    {
        return $this->repo->getMenus();
    }

    /**
     * @param string $route
     *
     * @return bool
     */
    public function isRouteScope($route)
    {
        $route = $this->repo->getRouteScope($route);

        return $route->isNotEmpty();
    }

    /**
     * 对比角色
     *
     * @param       $userId
     * @param array $data
     *
     * @return bool
     * @throws \Exception
     */
    public function contrastRole($userId, array $data = [])
    {
        /** @var User $user */
        $user = App::make(User::class);
        if ($user->getIsUltimate()) {
            return true;
        }
        $result = $this->repo->getSelfAndUserRole($userId);
        $selfRoles = $result['self'];
        $roles = $result['user'];

        $result = $selfRoles->first(function ($value) {
            return $value->name === '系统管理员';
        });

        if (! is_null($result)) {
            return true;
        }

        // 获取此次要改哪些角色
        $tempRoles = $roles->pluck('id');
        $changeRoles = $tempRoles->filter(function ($value) use ($data) {
            return ! in_array($value, $data);
        })->all();
        $temp = array_diff($data, $tempRoles->toArray());
        $changeRoles = array_merge($temp, $changeRoles);

        // 检查要修改的角色是不是在自己的名单里
        $check = $selfRoles->first(function ($value) use ($changeRoles) {
            return in_array($value->id, $changeRoles);
        });

        if (is_null($check)) {
            return false;
        }

        /*
         * 判断当前这个人是否能修改对方的角色
         * 过滤一下角色
         */
        $selfRoles = $selfRoles->filter(function ($value) use ($changeRoles){
            return in_array($value->id, $changeRoles);
        });

        $roles = $roles->filter(function ($value) use ($changeRoles){
            return in_array($value->id, $changeRoles);
        });

        foreach ($selfRoles as $selfRole) {
            foreach ($roles as $role) {
                if ($role->deep < $selfRole->deep && $role->root_id === $selfRole->root_id) {
                    return false;
                }
            }
        }

        return true;
    }
}