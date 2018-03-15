<?php
/**
 * Author: root
 * Date  : 17-4-29
 * time  : 下午12:38
 * Site  : www.ptbird.cn
 * There I am , in the world more exciting!
 */
namespace app\index\controller;
use think\Controller;

class Test extends Controller{
    public function index(){
        $appKey="LbiuELP0v78WI119884amA";
        $appId="krcCcKRoCi9f500A1ZA7VA";
        $masterSecret="iNTgE0fzM39mbHAUmKj2L8";
        $curtime=time();
        // 构建标识
        $sign=strtolower(hash_hmac('sha256',$appKey.$curtime.$masterSecret,false));
        $dataArr=[
            'sign:'.$sign,
            'timestamp:'.$curtime,
            'appkey:'.$appKey,
        ];
        // 构建header
        $header=array(
            'Content-Type: application/json',
        );
        $url='https://restapi.getui.com/v1/'.$appId.'/auth_sign';
        $res=curl_post($url,$header,$dataArr);
        echo json_encode($dataArr);
    }
}
