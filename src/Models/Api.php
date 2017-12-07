<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/15
 * Time: 16:45
 */

namespace SimpleShop\Permission\Models;


use Illuminate\Database\Eloquent\Model;

class Api extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'apis';

    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'path',
        'name',
        'module',
        'mark',
        'enable',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function menus()
    {
        return $this->belongsToMany('SimpleShop\Permission\Models\Menu', 'menu_api');
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
}