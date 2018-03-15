<?php
/**
 * Author: root
 * Date  : 17-5-9
 * time  : 下午2:27
 * Site  : www.ptbird.cn
 * There I am , in the world more exciting!
 */
namespace app\index\controller;

use app\index\model\Admin as AdminModel;
use think\Request;
use think\Session;

class Admin extends Acl{

    /**
     * 获取管理员信息
     */
    public function index(){
        $adminInfo=AdminModel::get(['guid',Session::get('loginadmin')]);
        $this->assign('adminInfo',$adminInfo);
        return $this->fetch();
    }
    /**
     *  修改密码
     */
    public function modify(Request $request){
        $p=trim($request->post('password'));
        $np=trim($request->post('newpassword'));
        $anp=trim($request->post('againnewpassword'));
        if($np!=$anp){
            Session::flash('err_msg','两次密码不一致');
            Session::flash('err_code',1);
            $this->redirect(url('/admin/info'));
        }
        $adminInfo=AdminModel::get(['guid',Session::get('loginadmin')]);
        if($adminInfo->password!=md5($p)){
            Session::flash('err_msg','原始密码错误');
            Session::flash('err_code',1);
            $this->redirect(url('/admin/info'));
        }
        $adminInfo->password=md5($np);
        if($adminInfo->save()){
            Session::flash('err_msg','修改成功');
            Session::flash('err_code',0);
            $this->redirect(url('/admin/info'));
        }else{
            Session::flash('err_msg','修改失败');
            Session::flash('err_code',1);
            $this->redirect(url('/admin/info'));
        }

        return $this->fetch();
    }
}
