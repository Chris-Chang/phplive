<?php
/**
 * Author: root
 * Date  : 17-4-1
 * time  : 下午8:43
 * Site  : www.ptbird.cn
 * There I am , in the world more exciting!
 */
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use think\Config;
use think\Cache;

class User extends Controller{
    /**
     *  初始化操作，确认用户登录
     *   - 请求session用户数据
     *   - 存在执行操作，不存在引导登录
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
            //如果是ajax请求，不应该跳转，应当返回错误值
            if (Request::instance()->isAjax()){
                $data=[
                    'code'=>400,
                    'status'=>0,
                    'msg'=>'请先登录'
                ];
                echo json_encode($data);exit();
            }else{
                $this->redirect(url('/login'));
            }
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
    public function index(){

    }
    /**
     * 显示用户的信息
     * @return mixed
     */
    public function showInfo(){
        // 用户的信息可以从缓存中获得 不需要查询数据库

        // 查询用户是否是主播
        $guid=Session::get('loginuser');
        $zhuboInfo=Db::name('userzhubo')->where(['user'=>$guid])->find();
        $userCheckFlag=0;
        $userCheckInfo=[];
        if(count($zhuboInfo)==0){
            $userZhuBoFlag=0;
            $userRoomInfo=[];
            // 不是主播查询是否提交过申请
            $userCheckInfo=Db::name('userzhubocheck')->where(['user'=>$guid])->find();
            if(count($userCheckInfo)>0){
                $userCheckInfo['update_time']=date('Y-m-d H:i:s',$userCheckInfo['update_time']);
                if($userCheckInfo['status'] == 0){
                    $userCheckFlag=2;
                }else{
                    $userCheckFlag=1;
                }

            }else{
                $userCheckFlag=0;
            }
        }else{
            $userZhuBoFlag=1;
            $userRoomInfo=Db::name('room')->where(['guid'=>$zhuboInfo['room']])->find();
            $userPassword=Db::name('user')->where(['guid'=>$guid])->field('password')->find();
            // 生成流密钥
            $userRoomInfo['roomPassword']=$userRoomInfo['guid']."?pass=".md5($userRoomInfo['guid'].$userPassword['password']);
        }
        $this->assign('userZhuBoFlag',$userZhuBoFlag);
        // 如果主播,则查询出房间的信息
        $this->assign('userRoomInfo',$userRoomInfo);
        // 不是主播 输出用户认证信息
        $this->assign('userCheckInfo',$userCheckInfo);
        $this->assign('userCheckFlag',$userCheckFlag);
        return $this->fetch();
     }
    /**
     *  修改用户信息
     *  - post nickname=nickanme usersex=usersex
     */
     public function editInfo(Request $request){
         $nickname=trim($request->param('nickname','post'));
         $sex=(int)trim($request->param('usersex','post'));
         if($sex !=0 && $sex!=1 && $sex!=2){
             $sex=0;
         }
         if(strlen($nickname)==0){
             Session::flash('err_msg','不能为空');
             Session::flash('err_code',1);
             $this->redirect(url('/user'));
         }
         // 写入数据库并且更新缓存
         $dataArr=[
            'sex'=>$sex,
             'nickname'=>$nickname
         ];
         $guid=Session::get('loginuser');
         if(Db::name('user')->where(['guid'=>$guid])->update($dataArr)){
             $userInfo = Cache::get($guid);
             $userInfo['nickname']=$nickname;
             $userInfo['sex']=$sex;
             Cache::set($guid,$userInfo);
             Session::flash('err_msg','修改成功');
             Session::flash('err_code',0);
             $this->redirect(url('/user'));
         }else{
             Session::flash('err_msg','修改失败或未发生修改');
             Session::flash('err_code',1);
             $this->redirect(url('/user'));
         }
     }

    /**
     * 修改密码
     * - post | password newpassword againpassword
     */
     public function editPassword(Request $request){
        // 获取参数
         $password=trim($request->param('password','post'));
         $newPassword=trim($request->param('newpassword','post'));
         $againPassword=trim($request->param('againpassword','post'));
        if(strlen($password)==0 || strlen($newPassword)==0 || strlen($againPassword)==0 ){
            Session::flash('err_msg','不能为空');
            Session::flash('err_code',1);
            $this->redirect(url('/user'));
        }
         if($newPassword != $againPassword){
             Session::flash('err_msg','两次密码不匹配');
             Session::flash('err_code',1);
             $this->redirect(url('/user'));
         }
         // 数据库中查出旧密码
         $guid=Session::get('loginuser');
         $oldPasswordArr=Db::name('user')->where(['guid'=>$guid])->field('password')->find();
         $oldPassword=$oldPasswordArr['password'];
         // 匹配旧密码
         if($oldPassword!=md5($password)){
             Session::flash('err_msg','旧密码验证错误');
             Session::flash('err_code',1);
             $this->redirect(url('/user'));
         }
         // 不能使用之前的密码
         if($password==$newPassword){
             Session::flash('err_msg','新旧密码应当不同');
             Session::flash('err_code',1);
             $this->redirect(url('/user'));
         }
         // 验证成功 写入新密码
         // 新密码进行md5加密
         $newPassword=md5($newPassword);
         $dataArr=[
             'password'=>$newPassword
         ];
         if(Db::name('user')->where(['guid'=>$guid])->setField($dataArr)){
             Session::flash('err_msg','修改成功,下次生效');
             Session::flash('err_code',0);
             $this->redirect(url('/user'));
         }else{
             Session::flash('err_msg','修改失败或者无修改');
             Session::flash('err_code',1);
             $this->redirect(url('/user'));
         }
     }

    /**
     * 修改房间的信息
     *  - post | name,notice,description
     */
     public function editRoom(Request $request){
         $name=trim($request->param('name','post'));
         $notice=trim($request->param('notice','post'));
         $description=trim($request->param('description','post'));
         // 不能为空
         if(strlen($name) == 0 || strlen($notice) == 0 || strlen($description) == 0){
             Session::flash('err_msg','不能为空');
             Session::flash('err_code',1);
             $this->redirect(url('/user'));
         }
         // 修改数据库
         // 从数据库中查出当前登录用户的房间号
         $guid=Db::name('userzhubo')->where(['user'=>Session::get('loginuser')])->field('room')->find();
         $guid=$guid['room'];
         // 修改新信息
         $dataArr=[
             'name'=>$name,
             'notice'=>$notice,
             'description'=>$description
         ];
         if(Db::name('room')->where(['guid'=>$guid])->update($dataArr)){
             Session::flash('err_msg','修改成功');
             Session::flash('err_code',0);
             $this->redirect(url('/user'));
         }else{
             Session::flash('err_msg','修改失败或者无修改');
             Session::flash('err_code',1);
             $this->redirect(url('/user'));
         }
     }

    /**
     * 上传房间封面
     */
     public function editRoomImage(){
         // 获取表单上传文件 例如上传了001.jpg
         $file = request()->file('roomimage');
         if($file===NULL){
             // 上传失败获取错误信息
             Session::flash('err_msg','必须选择一张图片');
             Session::flash('err_code',1);
             $this->redirect(url('/user'));
         }
         // 移动到框架应用根目录/public/uploads/ 目录下
         $info = $file->validate(['size'=>2097152,'ext'=>'jpg,png,jpeg'])->move(ROOT_PATH . 'public' . DS . 'static/images/room/');
         $saveName='dc6068e5-48c4-4611-a504-df4b652683de.jpg';
         if($info){
             // 成功上传后 获取上传信息
             // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
             $saveName=$info->getSaveName();
         }else{
             // 上传失败获取错误信息
             $uploadErr= $file->getError();
             Session::flash('err_msg',$uploadErr);
             Session::flash('err_code',1);
             $this->redirect(url('/user'));
         }
         // 写入数据库
         // 从数据库中查出当前登录用户的房间号
         $guid=Db::name('userzhubo')->where(['user'=>Session::get('loginuser')])->field('room')->find();
         $guid=$guid['room'];
         if(Db::name('room')->where(['guid'=>$guid])->setField('bgimgurl',$saveName)){
// 上传失败获取错误信息
             Session::flash('err_msg','成功修改封面');
             Session::flash('err_code',0);
             $this->redirect(url('/user'));
         }else{
             // 上传失败获取错误信息
             $uploadErr= $file->getError();
             Session::flash('err_msg','修改封面失败,请重试.');
             Session::flash('err_code',1);
             $this->redirect(url('/user'));
         }
     }
    /**
     *  主播验证申请
     *  - post |
     */
    public function zhuboCheck(){
        // 获取guid
        $guid=Session::get('loginuser');
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        if($file===NULL){
            // 上传失败获取错误信息
            Session::flash('err_msg','必须选择一张图片');
            Session::flash('err_code',1);
            $this->redirect(url('/user'));
        }
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['size'=>2097152,'ext'=>'jpg,png,jpeg'])->move(ROOT_PATH . 'public' . DS . 'static/images/teacher/');
        $saveName='demo.png';
        if($info){
            // 成功上传后 获取上传信息
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $saveName=$info->getSaveName();
        }else{
            // 上传失败获取错误信息
            $uploadErr= $file->getError();
            Session::flash('err_msg',$uploadErr);
            Session::flash('err_code',1);
            $this->redirect(url('/user'));
        }
        // 验证用户是否已经是主播
        // 是主播意味着已经提交了验证,否则就是没有提交验证或者正在验证期间
        $zhuboInfo=Db::name('userzhubo')->where(['user'=>$guid])->count();
        if($zhuboInfo!=0){
            Session::flash('err_msg','已经认证,不能再次认证');
            Session::flash('err_code',1);
            $this->redirect(url('/user'));
        }
        // 查询是否提交过验证
        $checkInfo=Db::name('userzhubocheck')->where(['user'=>$guid])->find();
        if(count($checkInfo)>0 && $checkInfo['status']==0){
            Session::flash('err_msg','已经提交过认证申请,请耐心等待');
            Session::flash('err_code',1);
            $this->redirect(url('/user'));
        }else if(count($checkInfo)>0 && $checkInfo['status']==2){
            // 提交失败 重新提交
            $time=time();
            $dataArr=[
                'image'=>$saveName,
                'update_time'=>$time,
                'status'=>0
            ];
            if(DB::name('userzhubocheck')->where(['user'=>$guid])->update($dataArr)){
                Session::flash('err_msg','成功提交认证申请,我们会尽快通过手机联系您!');
                Session::flash('err_code',0);
                $this->redirect(url('/user'));
            }else{
                Session::flash('err_msg','提交失败,请稍后重试');
                Session::flash('err_code',1);
                $this->redirect(url('/user'));
            }
        }else{
            // 没有提交过,数据表添加字段
            $time=time();
            $dataArr=[
                'user'=>$guid,
                'image'=>$saveName,
                'create_time'=>$time,
                'update_time'=>$time,
            ];
            if(Db::name('userzhubocheck')->insert($dataArr)){
                Session::flash('err_msg','成功提交认证申请,我们会尽快通过手机联系您!');
                Session::flash('err_code',0);
                $this->redirect(url('/user'));
            }else{
                Session::flash('err_msg','提交失败,请稍后重试');
                Session::flash('err_code',1);
                $this->redirect(url('/user'));
            }
        }
    }
    /**
     * 用户的收藏列表
     */
    public function myCollection(){
        // 根据usercollect user room 三张表查询
        $guid=Session::get('loginuser');
        // 查出房间的信息和房间主播的信息
        $dbPrefix = Config::get('database.prefix');
        $sql = "select user.nickname,user.guid as uid ,user.headimgurl , room.* from " . $dbPrefix . "user as user ," . $dbPrefix . "userzhubo as zhubo ," . $dbPrefix . "room as room,". $dbPrefix . "usercollection as usercollection where room.guid=usercollection.room AND room.disable=0 AND zhubo.room=room.guid AND user.guid=zhubo.user AND usercollection.user=? ORDER BY room.status desc , room.people desc ,room.update_time desc";
        // $this->assign('sql',$sql);
        // return $this->fetch();
        $roomInfo = Db::query($sql, [$guid]);
        // 输出信息
        $this->assign('roomInfo', $roomInfo);
        return $this->fetch();
    }
    /**
     * 用户的观看历史列表
     *   和收藏其实差不多 只不过中间表不一样
     */
    public function myHistory(){
        // 根据userhistory user room 三张表查询
        $guid=Session::get('loginuser');
        // 查出房间的信息和房间主播的信息
        $dbPrefix = Config::get('database.prefix');
        $sql = "select user.nickname,user.guid as uid ,user.headimgurl , room.* from " . $dbPrefix . "user as user ," . $dbPrefix . "userzhubo as zhubo ," . $dbPrefix . "room as room,". $dbPrefix . "userhistory as userhistory where room.guid=userhistory.room AND room.disable=0 AND zhubo.room=room.guid AND user.guid=zhubo.user AND userhistory.user=? ORDER BY room.status desc , room.people desc ,room.update_time desc";
        // $this->assign('sql',$sql);
        // return $this->fetch();
        $roomInfo = Db::query($sql, [$guid]);
        // 输出信息
        $this->assign('roomInfo', $roomInfo);
        return $this->fetch();
    }
    /**
     * 添加或者删除用户收藏的函数
     *  - post请求 参数为 room = guid
     *  - 失败的条件：用户未登录/room不存在或者被封禁/用户是主播/
     */
    public function toggleCollect(Request $request){
        $room=trim($request->param('room','post'));
        $dbPrefix = Config::get('database.prefix');
        // 查询房间信息,顺便将主播查询出来
        $sql="SELECT userzhubo.user as uid,room.* FROM ".$dbPrefix."userzhubo as userzhubo , ".$dbPrefix."room as room WHERE room.guid=? AND room.guid=userzhubo.room";
        $roomInfo=Db::query($sql,[$room]);
        $roomInfo=$roomInfo[0];
        if(count($roomInfo)==0){
            return json()->data(['code'=>400,'status'=>0,'msg'=>'房间信息错误']);
        }
        // 查出用户的guid
        $guid=Session::get('loginuser');
        if(strlen($guid)==0){
            return json()->data(['code'=>400,'status'=>0,'msg'=>'用户信息错误']);
        }
        if($guid==$roomInfo['uid']){
            return json()->data(['code'=>400,'status'=>0,'msg'=>'不能收藏自己']);
        }
        // 查看是否已经收藏过了 如果收藏过了，则直接取消收藏
        $userCollectonInfo=Db::name('usercollection')->where(['user'=>$guid,'room'=>$room])->find();

        if(count($userCollectonInfo)!=0){
            // 用户已经收藏过了 取消收藏操作
            if(Db::name('usercollection')->where(['user'=>$guid,'room'=>$room])->delete()){
                // 查看房间的收藏人数
                $count=Db::name('usercollection')->where(['room'=>$room])->count();
                return json()->data(['code'=>200,'status'=>1,'msg'=>'成功取消收藏','data'=>['number'=>$count]]);
            }else{
                return json()->data(['code'=>400,'status'=>0,'msg'=>'取消收藏失败',]);
            }
        }else{
            // 信息没有问题 写入数据库中 添加收藏信息
            $time=time();
            $data=['user'=>$guid,'room'=>$room,'create_time'=>$time,'update_time'=>$time];
            if(Db::name('usercollection')->insert($data)){
                // 插入成功 返回收藏成功
                $count=Db::name('usercollection')->where(['room'=>$room])->count();
                return json()->data(['code'=>200,'status'=>1,'msg'=>'成功收藏','data'=>['number'=>$count]]);
            }else{
                // 插入失败
                return json()->data(['code'=>400,'status'=>0,'msg'=>'收藏失败']);
            }
        }
    }
    /**
     * 举报房间
     */
    public function tipOff(Request $request){
       $guid=Session::get('loginuser');
        $roomGuid=$request->post('room');
        if(Cache::get("tipoff_".$guid."_".$roomGuid)){
            Session::flash('err_msg','已经对此课堂进行举报');
            Session::flash('err_code',1);
            $this->redirect(url('/live/'.$roomGuid));
        }
        // 写入
        $time=time();
        $dataArr=[
            'user'=>$guid,
            'room'=>$roomGuid,
            'tipoff_time'=>$time,
            'update_time'=>$time
        ];
        if(Db::name('usertipoff')->insert($dataArr)){
            // 存10分钟
            Cache::set("tipoff_".$guid."_".$roomGuid,600);
            Session::flash('err_msg','成功举报,将尽快处理');
            Session::flash('err_code',0);
            $this->redirect(url('/live/'.$roomGuid));
        }
    }
    /**
     *  修改头像
     */
    public function headimgurl(Request $request){
        $file=$request->file('headimgurl');
        $guid=Session::get('loginuser');
        if($file===NULL){
            // 上传失败获取错误信息
            Session::flash('err_msg','必须选择一张图片');
            Session::flash('err_code',1);
            $this->redirect(url('/user'));
        }
        // 移动到框架应用根目录/public/images/headimgurl/ 目录下
        $info = $file->validate(['size'=>2097152,'ext'=>'jpg,png,jpeg'])->move(ROOT_PATH . 'public' . DS . 'static/images/headimgurl/');
        $saveName='headimgurl.jpg';
        if($info){
            // 成功上传后 获取上传信息
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $saveName=$info->getSaveName();
        }else{
            // 上传失败获取错误信息
            $uploadErr= $file->getError();
            Session::flash('err_msg',$uploadErr);
            Session::flash('err_code',1);
            $this->redirect(url('/user'));
        }
        // 更新数据库
        $res=Db::name('user')->where(['guid'=>$guid])->setField('headimgurl',$saveName);
        // 更新缓存
        $userInfo = Cache::get($guid);
        $userInfo['headimgurl']=$saveName;
        $userInfo = Cache::set($guid,$userInfo);
        if($res){
            Session::flash('err_msg','修改头像成功');
            Session::flash('err_code',0);
            $this->redirect(url('/user'));
        }else{
            Session::flash('err_msg','修改失败');
            Session::flash('err_code',1);
            $this->redirect(url('/user'));
        }
    }
}
