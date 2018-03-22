<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/20
 * Time: 11:45
 */

namespace SimpleShop\Permission;


use SimpleShop\Permission\Contracts\MenuRepository;
use DB;

class Menu
{
    private $repo;

    public function __construct(MenuRepository $menuRepository)
    {
        $this->repo = $menuRepository;
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
        return DB::transaction(function () use ($data) {
            $result = $this->repo->store($data);
            $pid = empty($data['pid']) ? 0 : $data['pid'];


            if ((int)$pid !== 0) {
                // 计算根id
                $parent = $this->show($data['pid']);
                $rootId = $parent->root_id;
                // 计算深度
                $deep = (int)$parent->deep + 1;
                // 计算path
                $path = $parent->path . $result->id . ",";
            } else {
                // 计算根id
                $rootId = $result->id;
                // 计算深度
                $deep = 0;
                // 计算path
                $path = "," . $result->id . ",";
            }

            /*
             | 将根id和分类深度写回去
             | 使用save方法
             */
            $result->root_id = $rootId;
            $result->deep = $deep;
            $result->path = $path;
            if (!$result->save()) {
                throw new \Exception("商品分类没有添加成功");
            }

            return $result;
        });
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
     * @return mixed
     */
    public function show($id)
    {
        return $this->repo->find($id);
    }

    /**
     * 绑定API
     *
     * @param array $data
     */
    public function bindApi(array $data)
    {
        $this->repo->bind($data['menu_id'], $data['apis']);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Support\Collection
     */
    public function apis($id)
    {
        $data =  $this->repo->apis($id);

        return $data;
    }
}