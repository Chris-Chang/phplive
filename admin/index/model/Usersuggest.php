<?php
/**
 * Author: root
 * Date  : 17-4-23
 * time  : 下午2:49
 * Site  : www.ptbird.cn
 * There I am , in the world more exciting!
 */

namespace app\index\model;

use think\Model;

class Usersuggest extends Model{
    protected $name='usersuggest';
    protected function base($query){
        $query->order('update_time desc');
    }
}

