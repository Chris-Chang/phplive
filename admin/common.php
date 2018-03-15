<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 发送数据
 * @param String $url 请求的地址
 * @param array $header 自定义的header数据
 * @param array $content POST的数据
 * @return String
 */
function curl_post($url, $header, $content)
{
    $ch = curl_init();
    if (substr($url, 0, 5) == 'https') {
        // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 从证书中检查SSL加密算法是否存在
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    // 设置允许查看请求头信息
    // curl_setopt($ch,CURLINFO_HEADER_OUT,true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($content));
    $response = curl_exec($ch);
    // 查看请求头信息
    // dump(curl_getinfo($ch,CURLINFO_HEADER_OUT));
    if ($error = curl_error($ch)) {
        curl_close($ch);
        return $error;
    } else {
        curl_close($ch);
        return $response;
    }
}

/**
 * 生成guid函数
 * @return string guid
 */
function create_guid()
{
    if (function_exists('com_create_guid')) {
        return com_create_guid();//window下
    } else {//非windows下
        mt_srand((double)microtime() * 10000);//optional for php 4.2.0 andup.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $uuid = substr($charid, 0, 8)
            . substr($charid, 8, 4)
            . substr($charid, 12, 4)
            . substr($charid, 16, 4)
            . substr($charid, 20, 12);
        return strtolower($uuid);
    }
}
/**
 * 删除所有空格,用户防止sql注入,将空格都干掉就不存在sql简单的注入了
 * @param $str
 * @return mixed
 */
function trimAll($str)
{
    $qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
    return str_replace($qian,$hou,$str);
}

/**
 * 获取ip
 * @return array|false|string
 */
function getClientIp() {
    
    if (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
    }
    elseif (getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    }
    elseif (getenv('HTTP_X_FORWARDED')) {
        $ip = getenv('HTTP_X_FORWARDED');
    }
    elseif (getenv('HTTP_FORWARDED_FOR')) {
        $ip = getenv('HTTP_FORWARDED_FOR');

    }
    elseif (getenv('HTTP_FORWARDED')) {
        $ip = getenv('HTTP_FORWARDED');
    }
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}