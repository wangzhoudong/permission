<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/17
 * Time: 11:12
 */

namespace SimpleShop\Permission\Repositories;


use App\Exceptions\ResourcesNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use SimpleShop\Permission\Contracts\Repository;
use SimpleShop\Permission\Models\Api;

class ApiRepository implements Repository
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
    public function getList(
        array $search = [],
        int $limit = 20,
        int $page = 1,
        array $order = ['id' => 'desc'],
        array $columns = ['*']
    ) {
        $list = Api::search($search)->order($order)->paginate($limit, $columns, 'page', $page);

        return $list;
    }

    /**
     * @param array $search
     * @param array $order
     * @param array $columns
     *
     * @return Collection
     */
    public function getCollection(array $search = [], array $order = ['id' => 'desc'], array $columns = ['*'])
    {
        $data = Api::search($search)->get();

        return $data;
    }

    /**
     * 检查
     *
     * @param $id
     *
     * @return bool
     */
    public function find($id)
    {
        return Api::find($id);
    }

    /**
     * 保存
     *
     * @param array $data
     *
     * @return mixed
     */
    public function store(array $data)
    {
        return Api::create($data);
    }

    /**
     * 修改
     *
     * @param       $id
     * @param array $data
     *
     * @return mixed
     */
    public function update($id, array $data)
    {
        return Api::where('id', $id)->update($data);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        $this->unbind($id);
        return Api::destroy($id);
    }

    /**
     * 解除绑定
     *
     * @param       $id
     *
     * @return mixed
     */
    public function unbind($id)
    {
        $api = $this->find($id);

        if (! is_null($api)) {
            $api->menus()->detach();
        }
    }
}