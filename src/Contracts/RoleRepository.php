<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/28
 * Time: 14:47
 */

namespace SimpleShop\Permission\Contracts;


use Illuminate\Support\Collection;

interface RoleRepository extends Repository
{
    /**
     * 绑定到menu
     *
     * @param       $id
     * @param array $data
     *
     * @return mixed
     */
    public function bind($id, array $data);

    /**
     * 解除绑定
     *
     * @param       $id
     *
     * @return mixed
     */
    public function unbind($id);

    /**
     * @param
     *
     * @return Collection
     */
    public function menus($id);

    /**
     * 获取user对应的权限
     *
     * @param int|string|null $userId
     *
     * @return Collection
     */
    public function getUserRole($userId = null);

    /**
     * @param string|int      $userId
     * @param array $data
     *
     * @return mixed
     */
    public function bindUser($userId, array $data);

    /**
     * 获取带user权限的role列表
     *
     * @param int|string $id userId
     *
     * @return mixed
     */
    public function getUserRoles($id);

    /**
     * 判断该角色是否有子角色
     *
     * @param $id
     *
     * @return mixed
     */
    public function hasChild($id);
}