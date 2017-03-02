<?php
/**
 * Created by PhpStorm.
 * Author: Lin07ux
 * Date: 2016-07-06
 * Time: 10:39
 * Desc: 后台公共控制器
 */

namespace Admin\Controller;

use Think\Controller;

class CommonController extends Controller
{
    /**
     * @var int 登录会话有效期
     */
    protected $login_expiry = 1800;

    /**
     * 检查用户是否已经登录
     */
    public function _initialize()
    {
        // 如果未登录，对于 ajax 请求回复提示登录
        // 非 ajax 请求则直接跳转到登录页面
        if (!$this->checkLogin()) {
            $cur_url = urlencode(__SELF__);

            if (IS_AJAX) {
                $this->ajaxReturn([
                    'code' => 1,
                    'msg'  => '请先登录后再操作',
                    'data' => ['redirect' => U('User/login', ['from' => $cur_url])],
                ]);
            } else {
                redirect(U('User/login', ['from' => $cur_url]));
            }

            exit();
        }

        $this->assign('c', strtolower(CONTROLLER_NAME))
            ->assign('a', strtolower(ACTION_NAME));
    }

    /**
     * 检查用户的登录信息是否有效
     *
     * @return bool
     */
    protected function checkLogin()
    {
        if (session('user.id')) {
            $time = time();
            if (session('user.expiry') > $time) {
                session('user.expiry', $time + $this->login_expiry);

                return true;
            }
        }

        session('user', null);
        return false;
    }

    /**
     * 发送 404 响应
     */
    public function send404() {
        header("HTTP/1.1 404 Not Found");

        if (IS_AJAX) {
            $this->ajaxReturn(['code' => 404, 'msg' => '请求的资源或操作不存在']);
        } else {
            $this->display('Public/error404');
        }

        exit();
    }
}