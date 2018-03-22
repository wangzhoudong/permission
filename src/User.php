<?php
/**
 * Created by PhpStorm.
 * User: coffeekizoku
 * Date: 2017/11/29
 * Time: 09:51
 */

namespace SimpleShop\Permission;


use App\Exceptions\LoginException;
use SimpleShop\Permission\Contracts\User as UserInterface;
use Auth;

/**
 * user的数据对象
 *
 * Class User
 * @package SimpleShop\Permission
 */
class User implements UserInterface
{
    private $userId;

    private $isUltimate;

    public function __construct()
    {
        $this->setUser();
    }

    /**
     * 设置USER的值
     */
    public function setUser()
    {
        $user = Auth::user();

        if (! $user) {
            throw new LoginException('没有登录');
        }

        $this->userId = $user->id;
        $this->setIsUltimate($user);
    }

    /**
     * 获取user的实例
     *
     * @return \App\User|null
     */
    public function getId()
    {
        return $this->userId;
    }

    public function getIsUltimate()
    {
        return $this->isUltimate;
    }

    /**
     * @param $user
     *
     * @return bool
     */
    protected function setIsUltimate($user)
    {
        $admin = config('user.super_admin_users', 'admin@liweijia.com');

        $admin = explode('|', $admin);

        return $this->isUltimate = in_array($user->email, $admin);
    }
}