<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/16
 * Time: 16:35
 */

namespace SimpleShop\Permission\Models;


use Illuminate\Database\Eloquent\Model;

class MenuApi extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'menu_api';

    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'menu_id',
        'api_id'
    ];
}