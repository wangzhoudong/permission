<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/29
 * Time: 09:42
 */

namespace SimpleShop\Permission\Models;


use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'user_role';

    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'role_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}