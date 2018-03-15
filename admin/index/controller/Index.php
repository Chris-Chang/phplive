<?php
namespace app\index\controller;

use app\index\model\Cate as CateModel;
use app\index\model\Roomtype;
use app\index\model\User;
use think\Config;
use think\Db;
use think\Request;
use think\Session;
use app\index\model\Room;
use app\index\model\Usersuggest;
use app\index\model\Userzhubo;

class Index extends Acl
{
    /**
     * 判断用户是否登录，如果已经登录，则在header显示用户的头像
     * @return mixed
     */
    public function index()
    {
        // 获取当前系统中存正在直播的房间
        $roomAllInfo=Room::all();
        $roomNowInfo=Room::all(['status'=>1]);
        $userAllInfo=User::all();
        $userSuggestInfo=Usersuggest::all();
        $roomNowInfoCount=count($roomNowInfo);
        $this->assign('roomAllInfoCount',count($roomAllInfo));
        $this->assign('roomNowInfoCount',$roomNowInfoCount);
        $this->assign('roomNowInfo',$roomNowInfo);
        $this->assign('userAllInfoCount',count($userAllInfo));
        $this->assign('userSuggestInfoCount',count($userSuggestInfo));
        // 观看总人数
        $roomNowPeople=0;
        for($i=0;$i<$roomNowInfoCount;$i++){
            $roomNowPeople+=$roomNowPeople;
        }
        // 从数据库中查询每个房间的收藏量
        $collectionInfo=Db::name('usercollection')->field('room,count(room) as people')->group('room')->order('people desc')->limit('0,10')->select();
        $this->assign('collectionInfo',$collectionInfo);
        $this->assign('roomNowPeople',$roomNowPeople);
        return $this->fetch();
    }
    /**
     *  查看房间列表
     *  分页
     */
    public function roomList(){
        $roomAllInfo=Room::all();
        $this->assign('allRoomInfo',$roomAllInfo);
        return $this->fetch();
    }
    /**
     *  查看房间详情
     */
    public function roomItem($id){
        $guid=intval(trimAll($id));
        // 查询房间详细信息
        $roomInfo=Room::get(['guid'=>$guid]);
        if(!$roomInfo){
            Session::flash('err_msg','房间不存在');
            Session::flash('err_code',1);
            $this->redirect(url('/room/list'));
        }
        // 查询收藏的人
        $roomCollectionInfo=Db::name('user')->alias('user')->join('usercollection zhubo','zhubo.room = "'.$guid.'" AND zhubo.user=user.guid')->select();
        // 查询主播信息
        $zhuboInfo=Db::name('user')->alias('user')->join('userzhubo zhubo','zhubo.room = "'.$guid.'" AND zhubo.user=user.guid')->find();
        // 查询分类信息
        $cateList=CateModel::all();
        // 获取房间的type
        $type=Db::name('roomtype')->where(['room'=>$id])->field('type')->find();
        $typeInfo['id']=$type['type'];
        $name=Db::name('type')->where(['id'=>$typeInfo['id']])->field('name')->find();
        $typeInfo['name']=$name['name'];
        // 输出
        $this->assign('typeInfo',$typeInfo);
        $this->assign('cateList',$cateList);
        $this->assign('roomInfo',$roomInfo);
        $this->assign('roomCollectionInfo',$roomCollectionInfo);
        $this->assign('zhuboInfo',$zhuboInfo);
        return $this->fetch();
    }
    /**
     *  修改分类
     */
    public function roomCateModify(Request $request){
        $room=intval($request->post('room'));
        $type=intval($request->post('type'));
        // 修改
        $Userzhubo=new Roomtype(['room'=>$room]);
        if($Userzhubo->where(['room'=>$room])->update(['type'=>$type])){
            Session::flash('err_msg','修改分类成功');
            Session::flash('err_code',0);
            $this->redirect(url('/room/info/'.$room));
        }else{
            Session::flash('err_msg','修改分类失败');
            Session::flash('err_code',1);
            $this->redirect(url('/room/info/'.$room));
        }
    }
    /**
     * 封禁直播间
     */
    public function disableRoom(Request $request){
        $guid=intval(trimAll($request->post('room')));
        $roomInfo=Room::get(['guid'=>$guid]);
        $roomInfo->disable=1;
        $roomInfo->save();
        Session::flash('err_msg','成功封禁该直播间');
        Session::flash('err_code',0);
        $this->redirect(url('/room/info/'.$guid));
    }
    /**
     * 解除直播间的封禁
     */
    public function openDisableRoom(Request $request){
        $guid=intval(trimAll($request->post('room')));
        $roomInfo=Room::get(['guid'=>$guid]);
        $roomInfo->disable=0;
        $roomInfo->save();
        Session::flash('err_msg','直播间解除封禁成功');
        Session::flash('err_code',0);
        $this->redirect(url('/room/info/'.$guid));
    }
    /**
     * 列出提交认证申请房间
     */
    public function roomCheckList(){
        // 查询认证用户信息
        $zhuboCheckInfo=Db::name('user')->alias('user')->join('userzhubocheck zhubo','zhubo.user=user.guid')->order('zhubo.status asc')->select();
        $this->assign('zhuboCheckInfo',$zhuboCheckInfo);
        return $this->fetch();
    }
    /**
     * 通过教师认证申请
     */
    public function roomCheckSuccess(Request $request){
        $userGuid=trimAll($request->post('user'));
        $userNickname=trimAll($request->post('nickname'));
        // 写入数据表
        // 查看是否有房间
        $zhuboInfo=Db::name('userzhubo')->where(['user'=>$userGuid])->count();
        if($zhuboInfo>0){
            Session::flash('err_msg','教师已经认证,不可修改');
            Session::flash('err_code',1);
            $this->redirect(url('/room/check/list'));

        }
        // 没有房间创建一个房间
        $Room=new Room();
        $Room->name=$userNickname."的课堂";
        $Room->notice=$userNickname."开通直播课堂啦";
        $Room->description="这是".$userNickname."的直播课堂描述";
        $Room->save();
        $rid=$Room->guid;
        $Userzhubo=new Userzhubo();
        $Userzhubo->user=$userGuid;
        $Userzhubo->room=$rid;
        $Userzhubo->save();
        // 写入分类列表
        $Roomtype=new Roomtype();
        $Roomtype->type=1;
        $Roomtype->room=$rid;
        $Roomtype->save();
        // 讲认证修改成1
        Db::name('userzhubocheck')->where(['user'=>$userGuid])->setField('status',1);
        Session::flash('err_msg','新直播课堂成功建立');
        Session::flash('err_code',0);
        $this->redirect(url('/room/check/list'));

    }
    /**
     * 拒绝教师认证申请
     */
    public function roomCheckFail(Request $request){
        $userGuid=trimAll($request->post('user'));
        $userNickname=trimAll($request->post('nickname'));
        // 写入数据表
        // 查看是否有房间
        $zhuboInfo=Db::name('userzhubo')->where(['user'=>$userGuid])->count();
        if($zhuboInfo>0){
            Session::flash('err_msg','教师已经认证,不可修改');
            Session::flash('err_code',1);
            $this->redirect(url('/room/check/list'));
        }
        // 将认证修改成2
        Db::name('userzhubocheck')->where(['user'=>$userGuid])->setField('status',2);
        Session::flash('err_msg','已经拒绝该用户本次申请');
        Session::flash('err_code',0);
        $this->redirect(url('/room/check/list'));

    }
    /**
     * 违规举报列表
     */
    public function roomTipoffList(){
        // 列出违规举报的信息
        $zhuboCheckInfo=Db::name('user')->alias('user')->join('usertipoff tipoff','tipoff.user=user.guid')->join('room','room.guid=tipoff.room')->select();
        $this->assign('tipoffList',$zhuboCheckInfo);
        return $this->fetch();
    }
    /**
     * 建议反馈列表
     */
    public function suggestList(){
        //
        $list=Usersuggest::all();
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 删除反馈列表
     */
    public function suggestDelete(Request $request){
        //
        $id=intval($request->post('id'));
        if(Usersuggest::destroy(['id'=>$id])){
            Session::flash('err_msg','删除成功');
            Session::flash('err_code',0);
            $this->redirect(url('/suggest/list'));
        }else{
            Session::flash('err_msg','删除失败');
            Session::flash('err_code',1);
            $this->redirect(url('/suggest/list'));
        }
        return $this->fetch();
    }
}
