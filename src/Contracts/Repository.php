<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/16
 * Time: 16:44
 */

namespace SimpleShop\Permission\Contracts;


use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface Repository
{
    /**
     * 获取列表
     *
     * @param array $search
     * @param int   $limit
     * @param int   $page
     * @param array $order
     * @param array $columns
     *
     * @return LengthAwarePaginator
     */
    public function getList(array $search = [], int $limit = 20, int $page = 1, array $order = ['id' => 'desc'], array $columns = ['*']);

    /**
     * @param array $search
     * @param array $order
     * @param array $columns
     *
     * @return Collection
     */
    public function getCollection(array $search = [], array $order = ['id' => 'desc'], array $columns = ['*']);

    /**
     * 检查
     *
     * @param $id
     *
     * @return Model
     */
    public function find($id);

    /**
     * 保存
     *
     * @param array $data
     *
     * @return mixed
     */
    public function store(array $data);

    /**
     * 修改
     *
     * @param       $id
     * @param array $data
     *
     * @return mixed
     */
    public function update($id, array $data);

    /**
     * @param $id
     *
     * @return mixed
     */
    public function delete($id);
}