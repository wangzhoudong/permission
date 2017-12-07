<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/20
 * Time: 11:45
 */

namespace SimpleShop\Permission;


use SimpleShop\Permission\Repositories\ApiRepository;

class Api
{
    private $repo;

    public function __construct(ApiRepository $apiRepository)
    {
        $this->repo = $apiRepository;
    }

    /**
     * 获取列表
     *
     * @param array $search
     * @param int   $limit
     * @param int   $page
     * @param array $order
     * @param array $columns
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getList(array $search = [], int $limit = 20, int $page = 1, array $order = ['id' => 'desc'], array $columns = ['*'])
    {
        $list = $this->repo->getList($search, $limit, $page, $order, $columns);

        return $list;
    }

    /**
     * 获取集合
     *
     * @param array $search
     * @param array $order
     * @param array $columns
     *
     * @return \Illuminate\Support\Collection
     */
    public function getData(array $search = [], array $order = ['id' => 'desc'], array $columns = ['*'])
    {
        $data = $this->repo->getCollection($search);

        return $data;
    }

    /**
     * 保存
     *
     * @param array $data
     *
     * @return mixed
     * @throws \Exception
     */
    public function store(array $data)
    {
        return $this->repo->store($data);
    }

    /**
     * 修改
     *
     * @param       $id
     * @param array $data
     */
    public function update($id, array $data)
    {
        $this->repo->update($id, $data);
    }

    /**
     * 删除
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->repo->delete($id);
    }

    public function check()
    {

    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function show($id)
    {
        return $this->repo->find($id);
    }
}