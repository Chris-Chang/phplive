<?php
/**
 * Author: root
 * Date  : 17-4-23
 * time  : 下午2:43
 * Site  : www.ptbird.cn
 * There I am , in the world more exciting!
 */

namespace app\index\model;

use think\Model;

class User extends Model{
    protected $name='user';
    protected function base($query){
        // $query->where('status',1);
    }
    protected function getSexAttr($value){
        if($value==1){
            return '男';
        }else if($value == 2){
            return '女';
        }else{
            return '未知';
        }
    }
}

