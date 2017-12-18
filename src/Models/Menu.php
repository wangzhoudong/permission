<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/16
 * Time: 10:45
 */

namespace SimpleShop\Permission\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'menus';

    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'pid',
        'url',
        'name',
        'display',
        'path',
        'sort',
        'level',
        'ico',
        'mark',
        'show'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function apis()
    {
        return $this->belongsToMany('SimpleShop\Permission\Models\Api', 'menu_api');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function menuApi()
    {
        return $this->hasMany('SimpleShop\Permission\Models\MenuApi', 'menu_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('SimpleShop\Permission\Models\Role', 'role_menu');
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
        if (isset($search['module'])) {
            $query = $query->where($this->getTable() . '.module', $search['module']);
        }

        if (isset($search['show'])) {
            $query = $query->where($this->getTable() . '.show', $search['show']);
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
     * 根据userId出数据
     *
     * @param $query
     * @param $userId
     *
     * @return mixed
     */
    public function scopeUser($query, $userId)
    {
        return $query->join('role_menu', 'role_menu.menu_id', '=', $this->getTable() . '.id')->join('user_role', 'user_role.role_id', '=', 'role_menu.role_id')
            ->where('user_role.user_id', $userId);
    }
}