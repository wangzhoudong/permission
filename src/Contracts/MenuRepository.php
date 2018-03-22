<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/20
 * Time: 09:30
 */

namespace SimpleShop\Permission\Contracts;


use Illuminate\Support\Collection;

interface MenuRepository extends Repository
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
    public function apis($id);
}