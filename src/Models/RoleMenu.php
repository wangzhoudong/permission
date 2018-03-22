<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/16
 * Time: 16:36
 */

namespace SimpleShop\Permission\Models;


use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'role_menu';

    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'menu_id',
        'role_id'
    ];
}