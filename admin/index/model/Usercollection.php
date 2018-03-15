<?php
/**
 * Author: root
 * Date  : 17-4-26
 * time  : 下午8:30
 * Site  : www.ptbird.cn
 * There I am , in the world more exciting!
 */

namespace app\index\model;

use think\Model;

class Usercollection extends Model{
    protected $name='usercollection';
    // 获取房间名
    protected  function getRoomAttr($value){
        return $value;
    }
}
