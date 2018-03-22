<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/12/4
 * Time: 17:25
 */

namespace SimpleShop\Permission\Contracts;


interface PermissionRepository
{
    /**
     * @param  string    $api
     *
     * @return bool
     */
    public function check($api): bool;

    /**
     * 获取这个用户应该有的menu
     *
     * @return mixed
     */
    public function getMenus();
}