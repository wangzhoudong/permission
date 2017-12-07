<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/16
 * Time: 11:06
 */

namespace SimpleShop\Permission\Models;


use Illuminate\Database\Eloquent\Model;
use App;

class Role extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * 可以被集体附值的表的字段
     */
    protected $fillable = [
        'name',
        'level',
        'path',
        'mark',
        'root_id',
        'deep',
        'pid',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function menus()
    {
        return $this->belongsToMany('SimpleShop\Permission\Models\Menu', 'role_menu');
    }

    /**
     * 父级
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'id', 'pid');
    }

    /**
     * 寻找子集
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(static::class, 'pid', 'id');
    }

    /**
     * 搜索
     *
     * @param       $query
     * @param array $search
     *
     * @return mixed
     */
    public function scopeSearch($query, array $search = [])
    {
        if (isset($search['name'])) {
            $query = $query->where($this->getTable() . '.name', 'like', "%{$search['name']}%");
        }

        if (isset($search['status'])) {
            $query = $query->where($this->getTable() . '.status', $search['status']);
        }

        return $query;
    }

    /**
     * 排序
     *
     * @param       $query
     * @param array $order
     *
     * @return mixed
     */
    public function scopeOrder($query, array $order = [])
    {
        if (! empty($order)) {
            foreach ($order as $index => $item) {
                $query = $query->orderBy($index, $item);
            }
        }

        return $query;
    }

    /**
     * 和user表的scope
     *
     * @param      $query
     * @param null $userId
     *
     * @return mixed
     */
    public function scopeUser($query, $userId = null)
    {
        /** @var UserRole $userRole */
        $userRole = App::make(UserRole::class);

        $query = $query->leftJoin($userRole->getTable(), $userRole->getTable() . '.role_id', '=',
            $this->getTable() . '.id');
        if (! is_null($userId)) {
            $query = $query->where($userRole->getTable() . '.user_id', $userId);
        }

        return $query;
    }

    /**
     * 有user标记的role链表
     *
     * @param $query
     * @param $userId
     *
     * @return mixed
     */
    public function scopeUserTag($query, $userId)
    {
        /** @var UserRole $userRole */
        $userRole = App::make(UserRole::class);

        $query = $query->leftJoin($userRole->getTable(), function ($query) use ($userId, $userRole) {
            $query->on($userRole->getTable() . '.role_id', '=',
                $this->getTable() . '.id')->where($userRole->getTable() . '.user_id', '=', $userId);
        });

        return $query->select([
            $this->getTable() . '.id',
            $this->getTable() . '.name',
            $this->getTable() . '.pid',
            $userRole->getTable() . '.role_id',
            $userRole->getTable() . '.user_id',
        ]);
    }
}