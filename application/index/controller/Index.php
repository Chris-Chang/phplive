<?php
namespace app\index\controller;

use app\index\model\Type;
use app\index\model\Usersuggest;
use think\Cache;
use think\Config;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Index extends Controller
{
    /**
     * 判断用户是否登录，如果已经登录，则在header显示用户的头像
     * @return mixed
     */
    public function _initialize()
    {
        // 获取登录状态信息
        $guid = Session::get('loginuser');
        $logincheck = Session::get('logincheck');
        $checkCode = md5($guid . Config::get('login.flag'));
        if ($checkCode == $logincheck) {
            // 用户登录成功
            // 从缓存中读取用户的信息
            $userInfo = Cache::get($guid);
            if (!$userInfo) {
                // 未设置缓存，从数据库中查询数据并写入缓存
                $resInfo = Db::name('user')->where(['guid' => $guid])->find();
                if (count($resInfo) > 0) {
                    Cache::set($guid, $resInfo);
                }
                $userInfo = $resInfo;
            }
            // 上面只要用户登录 就确定查出了用户信息
            // 然后输出到前台，并且输出一个flag
            $this->assign('userLoginInfo', $userInfo);
            // dump($userInfo);
            $this->assign('userLoginFlag', 1);
        } else {
            // 用户没有触发登录条件，因此只要输出一个flag 0 即可
            $this->assign('userLoginFlag', 0);
            $this->assign('userLoginInfo', NULL);
        }
        /**
         *  获得所有的分类并且输出
         *  - 查询缓存中是否存在分类信息，如果存在则直接获取
         *  - 缓存中不存在分类信息，则从数据库读取所有的分类，然后加入缓存
         *  - 向所有的页面输出缓存中的分类信息
         */
        $roomTypeInfo=Cache::get('roomTypeInfo');
        if(!$roomTypeInfo){
            // 从数据库中读取相关的分类信息并写入缓存
            $roomTypeInfoRes=Db::name('type')->select();
            Cache::set('roomTypeInfo',$roomTypeInfoRes);
            $roomTypeInfo=$roomTypeInfoRes;
        }
        // 目前roomTypeInfo已经确认存在
        // 向所有页面输出
        $this->assign('roomTypeInfo',$roomTypeInfo);

        return false;
    }

    public function index()
    {
        // 获取当前所有在直播的房间的所有信息
        $dbPrefix = Config::get('database.prefix');
        $sql = "select user.nickname,user.guid as uid ,user.headimgurl , room.* from " . $dbPrefix . "user as user ," . $dbPrefix . "userzhubo as zhubo ," . $dbPrefix . "room as room where room.disable=0 AND zhubo.room=room.guid AND room.status=1  AND user.guid=zhubo.user order by room.update_time desc limit 0,9";
        // echo $sql;
        $zhiboRoomInfo = Db::query($sql);
        // dump($roomInfo);
        // 获取当前订阅排名前十的课程
        $sql="select *,count(*) as number from (select room.* from ".$dbPrefix."usercollection as usercollection , ".$dbPrefix."room as room where usercollection.room=room.guid ) as roomInfo group by roomInfo.guid order by number desc limit 0,10";
        // dump($sql);
        $topCollectonInfo = Db::query($sql);
        // dump($topCollectonInfo);
        $this->assign('zhiboRoomInfo', $zhiboRoomInfo);
        $this->assign('topCollectonInfo', $topCollectonInfo);
        return $this->fetch();
    }

    /**
     *  所有的直播房间
     */
    public function liveALl()

    {
        // 查询数据库中所有在直播的房间
        // 根据直播的时间进行逆序排序 —— update_time
        // 根据人数逆序排序
        $dbPrefix = Config::get('database.prefix');
        $sql = "select user.nickname,user.guid as uid ,user.headimgurl , room.* from " . $dbPrefix . "user as user ," . $dbPrefix . "userzhubo as zhubo ," . $dbPrefix . "room as room where room.disable=0 AND zhubo.room=room.guid AND room.status=1  AND user.guid=zhubo.user order by room.people desc,room.update_time desc ";
        $allPlayRoomInfo=Db::query($sql);
        $this->assign('allPlayRoomInfo',$allPlayRoomInfo);
        return $this->fetch();
    }
    /**
     *  所有没有在直播的房间
     *
     */
    public function liveAllNoPlay(){
        // 查询数据库中所有没直播的房间
        // 根据上次直播的时间进行逆序排序 —— update_time
        $dbPrefix = Config::get('database.prefix');
        $sql = "select user.nickname,user.guid as uid ,user.headimgurl , room.* from " . $dbPrefix . "user as user ," . $dbPrefix . "userzhubo as zhubo ," . $dbPrefix . "room as room where room.disable=0 AND zhubo.room=room.guid AND room.status=0  AND user.guid=zhubo.user order by room.update_time desc ";
        $allNoPlayRoomInfo=Db::query($sql);
        $this->assign('allNoPlayRoomInfo',$allNoPlayRoomInfo);
        return $this->fetch();
    }
    /**
     * 进入某个直播房间
     * @param $id
     */
    public function liveItem($id)
    {
        $id = trim($id);
        // 查询房间信息
        // 房间的type名称也查出来。
        $dbPrefix = Config::get('database.prefix');
        $sql = "select type.id as typeid ,type.name as type,user.nickname,user.guid as uid ,user.headimgurl , room.* from " . $dbPrefix . "user as user ," . $dbPrefix . "userzhubo as zhubo ," . $dbPrefix . "room as room ,". $dbPrefix. "roomtype as roomtype,". $dbPrefix. "type as type where room.guid=? AND zhubo.room=room.guid AND user.guid=zhubo.user AND roomtype.room=room.guid AND type.id=roomtype.type";
        // echo $sql;
        $roomInfo = Db::query($sql, [$id]);
        // 只选择第一条匹配的信息
        // dump($roomInfo);
        // 房间不存在返回主页
        if (count($roomInfo) == 0) {
            Session::flash('err_msg','房间不存在');
            Session::flash('err_code',1);
            $this->redirect(url('/'));
        }
        $roomInfo = $roomInfo[0];
        // 房间被封禁，返回主页
        if ($roomInfo['disable'] == 1) {
            Session::flash('err_msg','该房间违反规定已经被封禁');
            Session::flash('err_code',1);
            $this->redirect(url('/'));
        }
        // 查询房间订阅人数
        $roomCollection=Db::name('usercollection')->where(['room'=>$id])->select();
        // 查看用户是否已经订阅
        $roomCollectionFlag=0;
        $login_user=Session::get('loginuser');
        foreach ($roomCollection as $item){
            if(in_array($login_user,$item)){
                $roomCollectionFlag=1;
                break;
            }
        }
        // 如果用户已经登录 && 直播间已经开启
        // 则用户的观看历史就要写入或者更新,如果存在就更新update_time,如果不存在就insert
        if(strlen($login_user)>0 && $roomInfo['status']==1){
            // 存在登录的用户
            // 查询history是否存在数据
            $historyInfo=Db::name('userhistory')->where(['user'=>$login_user,'room'=>$id])->find();
            $time=time();
            if(count($historyInfo)==0){
                // 用户没有观看过 直接插入
                $dataArr=[
                    'user'=>$login_user,
                    'room'=>$id,
                    'create_time'=>$time,
                    'update_time'=>$time
                ];
                Db::name('userhistory')->insert($dataArr);
            }else{
                // 用户观看过,更新update_time
                Db::name('userhistory')->where(['user'=>$login_user])->setField('update_time',$time);
            }
        }
        $this->assign('roomCollecitonFlag',$roomCollectionFlag);
        // 输出房间信息
        $this->assign('roomCollection',count($roomCollection));
        $this->assign('roomInfo', $roomInfo);
        return $this->fetch();
    }
    /**
     *  查看某个分类下的所有的直播
     * @param $id int 分类的id
     */
    public function cateItem($id){
        // 查询分类的信息
        $cateInfo=Db::name('type')->where(['id'=>$id])->find();
        if(count($cateInfo)==0){
            $this->error('该分类不存在');
        }
        // 如果分类信息存在则查询相关的直播间
        // 查询某个分类下所有的直播间在直播的放在前面，不在直播的放在后面
        $dbPrefix = Config::get('database.prefix');
        $sql = "select user.nickname,user.guid as uid ,user.headimgurl , room.* from " . $dbPrefix . "user as user ," . $dbPrefix . "userzhubo as zhubo ," . $dbPrefix . "room as room,". $dbPrefix . "roomtype as roomtype where room.guid=roomtype.room AND room.disable=0 AND zhubo.room=room.guid AND user.guid=zhubo.user AND roomtype.type=? ORDER BY room.status desc , room.people desc ,room.update_time desc";
        // $this->assign('sql',$sql);
        // return $this->fetch();
        $roomInfo = Db::query($sql, [$cateInfo['id']]);
        // 输出信息
        $this->assign('cateInfo',$cateInfo);
        $this->assign('roomInfo', $roomInfo);
        return $this->fetch();
    }
    /**
     * 直播间的搜索
     */
    public function searchRoom(Request $request){
        $keyword=trimAll($request->param('keyword','post'));
        if(strlen($keyword)==0){
            $this->redirect(url('/live'));
        }
        // 查询
        $dbPrefix = Config::get('database.prefix');
        $sql = "select user.nickname,user.guid as uid ,user.headimgurl , room.* from " . $dbPrefix . "user as user ," . $dbPrefix . "userzhubo as zhubo ," . $dbPrefix . "room as room where  room.disable=0 AND zhubo.room=room.guid AND user.guid=zhubo.user AND room.name LIKE '%".$keyword."%' ORDER BY room.status desc , room.people desc ,room.update_time desc";
        $searchInfo= Db::query($sql);
        // 输出信息
        $this->assign('roomInfo', $searchInfo);
        return $this->fetch();
    }
    /**
     * 查看所有的分类
     */
    public function cateAll(){
        $typeList=Type::all();
        $this->assign('cateList',$typeList);
        return $this->fetch();
    }
    /**
     * 关于界面
     */
    public function about(){
       return $this->fetch();
    }
    /**
     * 提交反馈的界面
     */
    public function suggest(){
        return $this->fetch();
    }
    /**
     * 提交反馈的处理
     */
    public function suggestWork(Request $request){
        // 获取用户id
        $userGuid=Session::get('loginuser');
        $title=trimAll($request->post('title'));
        $content=trim($request->post('content'));
        $dataArr=[
            'title'=>$title,
            'user'=>$userGuid,
            'content'=>$content,
        ];
        $Usersuggest=new Usersuggest();
        if($Usersuggest->save($dataArr)){
            Session::flash('err_msg','提交反馈成功');
            Session::flash('err_code',0);
            $this->redirect(url('/suggest'));
        }else{
            Session::flash('err_msg','提交反馈失败');
            Session::flash('err_code',1);
            $this->redirect(url('/suggest'));
        }
    }
}
