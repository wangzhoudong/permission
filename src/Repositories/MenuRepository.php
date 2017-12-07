<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/17
 * Time: 16:18
 */

namespace SimpleShop\Permission\Repositories;


use App\Exceptions\ResourcesNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use SimpleShop\Permission\Contracts\MenuRepository as RepositoryInterface;
use SimpleShop\Permission\Models\Api;
use SimpleShop\Permission\Models\Menu;
use SimpleShop\Permission\Models\MenuApi;
use App;

class MenuRepository implements RepositoryInterface
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
        $list = Menu::search($search)->order($order)->paginate($limit, $columns, 'page', $page);

        return $list;
    }

    /**
     * @param array $search
     *
     * @param array $order
     * @param array $columns
     *
     * @return Collection
     */
    public function getCollection(
        array $search = [],
        array $order = ['path' => 'asc'],
        array $columns = ['*']
    ) {
        $data = Menu::search($search)->order($order)->get($columns);

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
        return Menu::find($id);
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
        $data = Menu::create($data);

        return $data;
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
        return Menu::where('id', $id)->update($data);
    }

    /**
     * 绑定到menu
     *
     * @param       $id
     * @param array $data
     *
     * @return mixed
     */
    public function bind($id, array $data)
    {
        $menu = $this->find($id);

        if (is_null($menu)) {
            throw new ResourcesNotFoundException('没有找到这个菜单');
        }

        $menu->apis()->sync($data);
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
        $menu = $this->find($id);

        if (! is_null($menu)) {
            $menu->apis()->detach();
        }
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        $this->unbind($id);

        return Menu::destroy($id);
    }

    /**
     * @param $id
     *
     * @return Collection
     */
    public function apis($id)
    {
        $api = App::make(Api::class);
        $menu = App::make(Menu::class);
        return $api->with(['menus' => function ($query) use ($id, $menu) {
            $query->where($menu->getTable() . '.id', $id);
        }])->where('enable', 1)->get();
    }
}