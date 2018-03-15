<?php
/**
 * Author: root
 * Date  : 17-3-27
 * time  : 上午12:33
 */
namespace app\index\controller;

use app\index\model\Admin;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use think\Config;
use think\Cache;

class Login extends Controller
{
    /**
     * 管理员登录页面
     * @return
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 管理员登录
     */
    public function loginWork(Request $request)
    {
        $admin = trim($request->param('name', 'post'));
        $password = trim($request->param('password', 'post'));

        // 查询用户信息
        $userInfo = Db::name('admin')->where(['name' => $admin])->find();
        $passwordCode = md5($password);
        // 用户不存在
        if (count($userInfo) == 0 || $passwordCode != $userInfo['password']) {
            $this->error('用户名或密码错误');
        }
        // 获取ip
        $ip=getClientIp();
        $dataArr=[
            'ip'=>$ip,
            'update_time'=>time()
        ];
        Db::name('admin')->where(['name' => $admin])->update($dataArr);
        // 登录成功
        Session::set('loginadmin', $userInfo['guid']);
        Session::set('loginflag', Config::get('login.adminflag'));
        Session::set('logincheck', md5($userInfo['guid'] . Config::get('login.adminflag')));
        Cache::set($userInfo['guid'], $userInfo);
        $this->redirect(url('/index'));
    }

    /**
     * 忘记密码页面
     */
    public function forgetPassword()
    {

    }

    /**
     * 忘记密码表单处理
     */
    public function forgetPasswordWork()
    {

    }

    /**
     * 登出
     */
    public function logout()
    {
        // 注册成功并且登录
        // 清楚缓存
        Cache::rm(Session::get('loginadmin'));
        // 清除session
        Session::delete('loginadmin');
        Session::delete('loginflag');
        Session::delete('logincheck');
        $this->redirect(url('/login'));
    }
}

