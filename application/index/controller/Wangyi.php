<?php
/**
 * Author: root
 * Date  : 17-3-31
 * time  : 上午12:21
 * Site  : www.ptbird.cn
 * There I am , in the world more exciting!
 */
namespace app\index\controller;

use think\Controller;
use think\Config;
use think\Request;

class Wangyi extends Controller
{
    /**
     * 发送验证码
     * @param '' none
     * @return '' none
     */
    public function message($phone = "")
    {
        $header = $this->getHeader();
        // 构建data
        $data = array(
            'mobile' => $phone,
            'templateid' => 3059207,
        );
        // URL
        $url = 'https://api.netease.im/sms/sendcode.action';
        // 发送短信验证码
        $res = curl_post($url, $header, $data);
        $res = json_decode($res, true);
        // "{"code":200,"msg":"101","obj":"6929"}"
        if ($res['code'] == 200) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 得到头信息
     * @param  none
     * @return array $header - 头信息数组
     */
    public function getHeader()
    {
        // 构建checksum
        $curtime = time();
        $nonce = rand();
        $checksum = strtolower(sha1(Config::get('wangyi.appsecret') . $nonce . $curtime));
        // 构建header
        $header = array(
            'AppKey:' . Config::get('wangyi.appkey'),
            'CurTime:' . $curtime,
            'CheckSum:' . $checksum,
            'Nonce:' . $nonce,
            'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
        );
        return $header;
    }

    /**
     * 校验验证码
     *
     */
    public function verifyMessage($phone = "", $code = "")
    {
        $header = $this->getHeader();
        $url = 'https://api.netease.im/sms/verifycode.action';
        $data = [
            'mobile' => $phone,
            'code' => $code,
        ];
        $res = curl_post($url, $header, $data);
        $res = json_decode($res, true);
        if ($res['code'] == 200) {
            return true;
        } else {
            return false;
        }
    }
}
