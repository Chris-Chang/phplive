<?php
/**
 * Author: root
 * Date  : 17-5-9
 * time  : 上午12:11
 * Site  : www.ptbird.cn
 * There I am , in the world more exciting!
 */

namespace app\index\controller;
use app\index\model\Room;
use app\index\model\User as UserModel;
use app\index\model\Usercollection as UsercollectionModel;
use app\index\model\Userhistory as UserhistoryModel;
use app\index\model\Userzhubo;
use think\Request;
use think\Session;

class User extends Acl{

    /**
     * 用户列表
     */
    public function index(){
        // 获取所有列表
        $userList=UserModel::all(['status'=>1]);
        $this->assign('userList',$userList);
        return $this->fetch();
    }
    /**
     * 封禁的列表
     */
    public function disableList(){
        // 获取所有列表
        $userList=UserModel::all(['status'=>0]);
        $this->assign('userList',$userList);
        return $this->fetch();
    }
    /**
     * 用户的详细信息
     */
    public function userItem($id){
        $guid=trimAll($id);
        // 数据库查询
        $userInfo=UserModel::get($guid);
        if(!$userInfo){
            Session::flash('err_msg','用户不存在');
            Session::flash('err_code',1);
            $this->redirect(url('/user/list'));
        }
        // 用户的收藏
        $userCollectionInfo=UsercollectionModel::all(['user'=>$guid]);
        // 用户的历史
        $userHistoryInfo=UserhistoryModel::all(['user'=>$guid]);
        // 用户的主播情况
        $userZhuboInfo=Userzhubo::get(['user'=>$guid]);
        $userzhuboFlag=0;
        if($userZhuboInfo){
            $userzhuboFlag=1;
            // 获取课堂信息
            $userRoomInfo=Room::get(['guid'=>$userZhuboInfo->room]);
            $this->assign('userRoomInfo',$userRoomInfo);
        }
        $this->assign('userItemInfo',$userInfo->toArray());
        $this->assign('userCollectionInfo',$userCollectionInfo);
        $this->assign('userHistoryInfo',$userHistoryInfo);
        $this->assign('userZhuboInfo',$userZhuboInfo);
        $this->assign('userzhuboFlag',$userzhuboFlag);
        return $this->fetch();
    }
    /**
     * 封禁或者解除封禁
     */
    public function userDisable(Request $request){
        $status=intval($request->post('status'));

        $userGuid=$request->post('user');
        $User=UserModel::get(['guid'=>$userGuid]);
        if(!$User){
            Session::flash('err_msg','用户不存在');
            Session::flash('err_code',1);
            $this->redirect(url('/user/list'));
        }
        switch ($status){
            case 1: $status=0;$message="成功封禁用户";break;
            default:$status=1;$message="解除用户封禁成功";break;
        }
        $User->status=$status;
        if($User->save()){
            Session::flash('err_msg',$message);
            Session::flash('err_code',0);
            $this->redirect(url('/user/info/'.$userGuid));
        }
    }
}