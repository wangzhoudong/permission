<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/29
 * Time: 09:45
 */

namespace SimpleShop\Permission\Contracts;


/**
 * User的实体数据接口
 *
 * Interface User
 * @package SimpleShop\Permission\Contracts
 */
interface UserContract
{
    /**
     * 获取id
     *
     * @return mixed
     */
    public function getId();

    /**
     * 获取是否是特级管理员
     *
     * @return mixed
     */
    public function getIsUltimate();
}