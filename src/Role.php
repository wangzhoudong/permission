<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/28
 * Time: 14:46
 */

namespace SimpleShop\Permission;


use App\Exceptions\LoginException;
use App\Exceptions\ServiceErrorException;
use Illuminate\Database\Eloquent\Model;
use SimpleShop\Permission\Contracts\RoleRepository;
use SimpleShop\Permission\Contracts\User;
use DB;
use SimpleShop\Permission\Models\Role as RoleModel;

class Role
{
    private $repo;

    private $user;

    public function __construct(RoleRepository $roleRepository, User $user)
    {
        $this->repo = $roleRepository;
        $this->user = $user;
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
    public function getList(
        array $search = [],
        int $limit = 20,
        int $page = 1,
        array $order = ['id' => 'desc'],
        array $columns = ['*']
    ) {
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
            if (! $result->save()) {
                throw new \Exception("角色没有添加成功");
            }

            $bool = $this->checkUserRole($result);
            if (! $bool) {
                throw new LoginException('不能操作比你等级高的角色');
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
        $role = $this->show($id);
        $bool = $this->checkUserRole($role);

        if (! $bool) {
            throw new LoginException('不能操作比你等级高的角色');
        }

        $this->repo->update($id, $data);
    }

    /**
     * 删除
     *
     * @param $id
     */
    public function delete($id)
    {
        $role = $this->show($id);
        $bool = $this->checkUserRole($role);

        if (! $bool) {
            throw new LoginException('不能操作比你等级高的角色');
        }

        // 判断是否有子分类
        if ($this->repo->hasChild($id)) {
            throw new ServiceErrorException('该分类下有子分类,不能删除');
        }

        $this->repo->delete($id);
    }

    /**
     * @param $id
     *
     * @return Model
     */
    public function show($id)
    {
        return $this->repo->find($id);
    }

    /**
     * 绑定菜单
     *
     * @param array $data
     */
    public function bindMenus(array $data)
    {
        $role = $this->show($data['role_id']);
        $bool = $this->checkUserRole($role);

        if (! $bool) {
            throw new LoginException('不能操作比你等级高的角色');
        }

        $this->repo->bind($data['role_id'], $data['menus']);
    }

    /**
     * 关联的菜单列表
     *
     * @param $id
     *
     * @return \Illuminate\Support\Collection
     */
    public function menus($id)
    {
        return $this->repo->menus($id);
    }

    /**
     * @param Model $role
     *
     * @return bool
     */
    protected function checkUserRole(Model $role)
    {
        // 如果是终极管理员,直接通过
        if ($this->user->getIsUltimate()) {
            return true;
        }

        $roles = $this->repo->getUserRole($this->user->getId());

        /*
         * | 检查当前用户对应的role是否能修改目标role
         * | 暂定只能由自己的上级或添加
         */
        foreach ($roles as $item) {
            if ($item->pid === 0) {
                return true;
            }
            if ($item->deep > $role->deep && $item->root_id === $role->root_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * 绑定用户
     *
     * @param       $userId
     * @param array $data
     *
     * @return mixed
     * @throws \Exception
     */
    public function bindUser($userId, array $data)
    {
        // 检查要绑定的角色是不是比你等级高
        $user = \Auth::user();
        /** @var Permission $permission */
        $permission = \App::makeWith(Permission::class, ['user' => $user]);
        $bool = $permission->contrastRole($userId);
        if ($bool) {
            return $this->repo->bindUser($userId, $data);
        }

        throw new \Exception('你不能操作这个用户的角色');
    }

    /**
     * 获取带user的roles
     *
     * @param $id
     *
     * @return mixed
     */
    public function getUserRoles($id)
    {
        return $this->repo->getUserRoles($id);
    }
}