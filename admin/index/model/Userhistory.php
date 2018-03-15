<?php
/**
 * Author: root
 * Date  : 17-5-9
 * time  : 上午12:53
 * Site  : www.ptbird.cn
 * There I am , in the world more exciting!
 */
namespace app\index\model;

use think\Model;

class Userhistory extends Model{

    protected $name='userhistory';

    // 获取房间名
    protected  function getRoomAttr($value){
        $new=$value."-".Room::get(['guid'=>$value]);
        return $new;
    }
}
