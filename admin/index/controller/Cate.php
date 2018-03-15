<?php
/**
 * Author: root
 * Date  : 17-5-9
 * time  : 下午12:02
 * Site  : www.ptbird.cn
 * There I am , in the world more exciting!
 */

namespace app\index\controller;

use app\index\model\Roomtype;
use think\Controller;
use think\Request;
use app\index\model\Cate as CateModel;
use think\Session;

class Cate extends Acl{
    /**
     * 分类列表
     */
    public function index(){
        // 获取列表
        $cateList=CateModel::all();
        $this->assign('cateList',$cateList);
        return $this->fetch();
    }
    /**
     *  修改分类名称
     */
    public function cateModify(Request $request){
        //
        $id=intval($request->post('id'));
        $name=trimAll($request->post('name'));
        // 更新
        $Cate=CateModel::get(['id'=>$id]);
        if(!$Cate){
            Session::flash('err_msg','分类不存在');
            Session::flash('err_code',1);
            $this->redirect(url('/cate/list'));
        }
        $Cate->name=$name;
        if($Cate->save()){
            Session::flash('err_msg','修改成功');
            Session::flash('err_code',0);
            $this->redirect(url('/cate/list'));
        }else{
            Session::flash('err_msg','修改失败');
            Session::flash('err_code',1);
            $this->redirect(url('/cate/list'));
        }
    }
    /**
     *  增加分类
     */
    public function cateAdd(Request $request){
        //
        $name=trimAll($request->post('name'));
        if(strlen($name)==0){
            Session::flash('err_msg','不能为空');
            Session::flash('err_code',1);
            $this->redirect(url('/cate/list'));
        }
        $Cate=new CateModel();
        $Cate->name=$name;
        if($Cate->save()){
            Session::flash('err_msg','增加成功');
            Session::flash('err_code',0);
            $this->redirect(url('/cate/list'));
        }else{
            Session::flash('err_msg','增加失败');
            Session::flash('err_code',1);
            $this->redirect(url('/cate/list'));
        }
    }
    /**
     *  删除分类
     */
    public function cateDelete(Request $request){
        $id=intval($request->post('id'));
        if(CateModel::destroy(['id'=>$id])){
            Session::flash('err_msg','删除成功');
            Session::flash('err_code',0);
            $this->redirect(url('/cate/list'));
        }else{
            Session::flash('err_msg','删除失败');
            Session::flash('err_code',1);
            $this->redirect(url('/cate/list'));
        }
    }
    /**
     * 分类下的房间
     */
    public function cateItem($id){
        $id=intval($id);
        // 获取分类信息
        $Cate=CateModel::get(['id'=>$id]);
        if(!$Cate){
            Session::flash('err_msg','分类不存在');
            Session::flash('err_code',1);
            $this->redirect(url('/cate/list'));
        }
        //
        $this->assign('cateInfo',$Cate);
        $list=Roomtype::all(['type'=>$id]);
        $this->assign('list',$list);
        return $this->fetch();
    }
}