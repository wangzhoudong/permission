<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/28
 * Time: 14:47
 */

namespace SimpleShop\Permission\Repositories;


use App\Exceptions\ResourcesNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use SimpleShop\Permission\Contracts\RoleRepository as RepositoryInterface;
use SimpleShop\Permission\Contracts\UserContract;
use SimpleShop\Permission\Models\Menu;
use SimpleShop\Permission\Models\Role;
use App;
use SimpleShop\Permission\Models\UserRole;
use Auth;

class RoleRepository implements RepositoryInterface
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
        $list = Role::search($search)->order($order)->paginate($limit, $columns, 'page', $page);

        return $list;
    }

    /**
     * 获取集合
     *
     * @param array $search
     * @param array $order
     * @param array $columns
     *
     * @return Collection
     */
    public function getCollection(array $search = [], array $order = ['id' => 'desc'], array $columns = ['*'])
    {
        $data = Role::search($search)->order($order)->get($columns);

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
        return Role::find($id);
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

        $data = Role::create($data);

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
        return Role::where('id', $id)->update($data);
    }

    /**
     * 删除
     *
     * @param $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        $this->unbind($id);

        return Role::destroy($id);
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
        $role = $this->find($id);

        if (is_null($role)) {
            throw new ResourcesNotFoundException('没有找到这个权限');
        }

        $role->menus()->sync($data);
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
        $role = $this->find($id);

        if (! is_null($role)) {
            $role->menus()->detach();
        }
    }

    /**
     * 获取菜单
     *
     * @param
     *
     * @return Collection
     */
    public function menus($id)
    {
        $menu = App::make(Menu::class);
        $role = App::make(Role::class);

        $user = App::make(UserContract::class);

        if (! $user->getIsUltimate()) {
            $selfRoles = $role->join('user_role', 'user_role.role_id', '=', $role->getTable() . '.id')->where('user_role.user_id', Auth::user()->id)->get();
            $result = $selfRoles->first(function ($item) {
                return $item->name === '系统管理员';
            });
            if (is_null($result)) {
                $menu = $menu->join('role_menu', 'role_menu.menu_id', '=', $menu->getTable() . '.id')->join('user_role', 'user_role.role_id', '=', 'role_menu.role_id')->where('user_role.user_id', Auth::user()->id);
            }
        }

        return $menu->with([
            'roles' => function ($query) use ($id, $role) {
                $query->where($role->getTable() . '.id', $id);
            },
        ])->get();
    }

    /**
     * @param int|string|null $userId
     *
     * @return Collection
     */
    public function getUserRole($userId = null)
    {
        return Role::user($userId)->get();
    }

    /**
     * @param string|int $userId
     * @param array      $data
     *
     * @return mixed
     */
    public function bindUser($userId, array $data)
    {
        UserRole::where('user_id', $userId)->delete();

        $insert = [];
        foreach ($data as $datum) {
            $insert[] = [
                'role_id' => $datum,
                'user_id' => $userId,
            ];
        }

        UserRole::insert($insert);

        return true;
    }

    /**
     * @param int|string $id userId
     *
     * @return mixed
     */
    public function getUserRoles($id)
    {
        return Role::userTag($id)->get();
    }

    /**
     * 判断该角色是否有子角色
     *
     * @param $id
     *
     * @return bool
     */
    public function hasChild($id)
    {
        $count = Role::whereRaw("FIND_IN_SET('1', `path`) AND roles.deep > (SELECT deep FROM roles AS r WHERE r.id = ?)",
            [$id])->count();

        return $count > 0;
    }
}