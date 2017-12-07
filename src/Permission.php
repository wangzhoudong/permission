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
}