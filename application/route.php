<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

Route::rule('/'                    ,'index/Index/index'                 ,'GET');
Route::rule('/index'               ,'index/Index/index'                 ,'GET');
Route::rule('/about'               ,'index/Index/about'                 ,'GET');
Route::rule('/suggest'               ,'index/Index/suggest'                 ,'GET');
Route::rule('/suggest'               ,'index/Index/suggestWork'                 ,'POST');

// 注册视频播放
Route::rule('/live/:id'            ,'index/Index/liveItem'              ,'GET');
Route::rule('/live'                ,'index/Index/liveAll'               ,'GET');
Route::rule('/live/all'            ,'index/Index/liveAll'               ,'GET');
Route::rule('/live/all/noplay'     ,'index/Index/liveAllNoPlay'         ,'GET');
Route::rule('/live/search'         ,'index/Index/searchRoom'            ,'POST');

// 注册分类相关
Route::rule('/cate/all'            ,'index/Index/cateAll'               ,'GET');
Route::rule('/cate/:id'            ,'index/Index/cateItem'              ,'GET');
Route::rule('/cate'                ,'index/Index/liveAll'               ,'GET');

// 注册登录忘记密码
Route::rule('/login'               ,'index/Login/index'                 ,'GET');
Route::rule('/login/work'          ,'index/Login/loginWork'             ,'POST');
Route::rule('/regist'              ,'index/Login/regist'                ,'GET');
Route::rule('/regist/sendcode'     ,'index/Login/registSendCode'        ,'POST');
Route::rule('/regist/checkcode'    ,'index/Login/registCheckCode'       ,'POST');
Route::rule('/regist/work'         ,'index/Login/registWork'            ,'POST');
Route::rule('/login/passwprd'      ,'index/Login/forgetPassword'        ,'GET');
Route::rule('/login/passwprd/work' ,'index/Login/forgetPasswordWork'    ,'POST');
Route::rule('/login/logout'        ,'index/Login/logout'                ,'GET');

// 注册用户相关路由
Route::rule('/user'                ,'index/User/showInfo'               ,'GET');
Route::rule('/user/info'           ,'index/User/showInfo'               ,'GET');
Route::rule('/user/edit'           ,'index/User/editInfo'               ,'POST');
Route::rule('/user/password'       ,'index/User/editPassword'           ,'POST');
Route::rule('/user/collect'        ,'index/User/toggleCollect'          ,'POST');
Route::rule('/user/mycollection'   ,'index/User/myCollection'           ,'GET');
Route::rule('/user/myhistory'      ,'index/User/myHistory'              ,'GET');
Route::rule('/user/zhubo'          ,'index/User/zhuboCheck'             ,'POST');
Route::rule('/user/room/edit'      ,'index/User/editRoom'               ,'POST');
Route::rule('/user/room/image'     ,'index/User/editRoomImage'          ,'POST');
Route::rule('/user/tipoff'     ,'index/User/tipOff'          ,'POST');
Route::rule('/user/headimgurl'     ,'index/User/headimgurl'          ,'POST');

// 注册直播相关事件回调
Route::rule('/on_publish'          ,'index/Rtmp/onPublish');         // 推流输出
Route::rule('/on_publish_done'     ,'index/Rtmp/onPublishDone');     // 推流结束
Route::rule('/on_play'             ,'index/Rtmp/onPlay');           // 播放
Route::rule('/on_play_done'        ,'index/Rtmp/onPlayDone');       // 客户端播放结束
Route::rule('/ffmpeg_photo'        ,'index/Rtmp/ffmpegPhoto');      // 截取视频流截图

// 注册gateway相关事件
Route::rule('/gateway/bind'          ,'index/GatewayServer/bind');         // 绑定
Route::rule('/gateway/send'          ,'index/GatewayServer/send');         // 绑定
Route::rule('/test'          ,'index/Test/index');         // 绑定
