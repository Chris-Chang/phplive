<?php
/**
 * Author: root
 * Date  : 17-3-27
 * time  : 上午12:33
 */
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use app\index\controller\Wangyi;
use think\Session;
use think\Config;
use think\Cache;

class Login extends Controller
{
    /**
     * 用户登录页面
     * @return
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 用户登录表单提交
     */
    public function loginWork(Request $request)
    {
        $phone = trim($request->param('phone', 'post'));
        $password = trim($request->param('password', 'post'));

        // 查询用户信息
        $userInfo = Db::name('user')->where(['phone' => $phone])->find();
        $passwordCode = md5($password);
        // 用户被封禁
        if ($userInfo['status'] == 0) {
            $this->error('登录失败');
        }
        // 用户不存在
        if (count($userInfo) == 0 || $passwordCode != $userInfo['password']) {
            $this->error('用户或密码错误');
        }
        // 登录成功
        Session::set('loginuser', $userInfo['guid']);
        Session::set('loginflag', Config::get('login.flag'));
        Session::set('logincheck', md5($userInfo['guid'] . Config::get('login.flag')));
        Cache::set($userInfo['guid'], $userInfo);
        $this->redirect(url('/index'));
    }

    /**
     * 注册界面
     */
    public function regist()
    {

        return $this->fetch();
    }

    /**
     * 注册界面 发送验证码
     */
    public function registSendCode(Wangyi $wx, Request $request)
    {
        $phone = trim($request->param('phone'));
        if (strlen($phone) != 11) {
            $data = [
                'code' => '400',
                'status' => 'error',
                'msg' => '手机号格式错误',
            ];
            return json()->data($data);
        }
        if ($this->checkPhoneExist($phone)) {
            $data = [
                'code' => '400',
                'status' => 'error',
                'msg' => '该手机号已经注册过',
            ];
            return json()->data($data);
        }
        //if ($wx->message($phone)) {
        if (true) {
            $data = [
                'code' => '200',
                'status' => 'ok',
                'msg' => '发送成功',
            ];
            return json()->data($data);
        } else {
            $data = [
                'code' => '400',
                'status' => 'error',
                'msg' => '验证码发送失败',
            ];
            return json()->data($data);
        }
    }

    /**
     *  查看用户手机号是否已经被注册
     *
     */
    public function checkPhoneExist($phone = "")
    {
        $info = Db::name('user')->where(['phone' => $phone])->find();
        if (count($info) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 注册界面 验证验证码
     */
    public function registCheckCode()
    {

    }

    /**
     * 注册表单提交处理
     */
    public function registWork(Request $request, Wangyi $wy)
    {
        $code = trim($request->param('code'));
        $name = trim($request->param('name'));
        $phone = trim($request->param('phone'));
        $password = trim($request->param('password'));
        $againpassword = trim($request->param('againpassword'));
        if (strlen($name) == 0 || strlen($code) == 0 || strlen($phone) != 11 || strlen($password) == 0 || strlen($againpassword) == 0 || $password != $againpassword) {
            $data = [
                'code' => '400',
                'status' => 'error',
                'msg' => '格式错误',
            ];
            return json()->data($data);
        }
        if ($this->checkPhoneExist($phone)) {
            $data = [
                'code' => '400',
                'status' => 'error',
                'msg' => '该手机号已经注册过',
            ];
            return json()->data($data);
        }
        // 检查短信验证码
       // if (!$wy->verifyMessage($phone, $code)) {
        if (false) {
            $data = [
                'code' => '400',
                'status' => 'error',
                'msg' => '短信验证码错误',
            ];
            return json()->data($data);
        }
        $guid = create_guid();
        // 写入数据库
        $arr = [
            'nickname' => $name,
            'guid' => $guid,
            'password' => md5($password),
            'phone' => $phone,
            'create_time' => time(),
            'update_time' => time()
        ];
        if (Db::name('user')->insert($arr)) {
            $data = [
                'code' => '200',
                'status' => 'ok',
                'msg' => '用户注册成功',
            ];
            // 注册成功并且登录
            Session::set('loginuser', $arr['guid']);
            Session::set('loginflag', Config::get('login.flag'));
            Session::set('logincheck', md5($arr['guid'] . Config::get('login.flag')));
            return json()->data($data);
        } else {
            $data = [
                'code' => '400',
                'status' => 'error',
                'msg' => '注册失败',
            ];
            return json()->data($data);
        }
    }

    /**
     * 登出
     */
    public function logout()
    {
        // 注册成功并且登录
        // 清楚缓存
        Cache::rm(Session::get('loginuser'));
        // 清除session
        Session::delete('loginuser');
        Session::delete('loginflag');
        Session::delete('logincheck');
        $this->redirect(url('/'));
    }
}

