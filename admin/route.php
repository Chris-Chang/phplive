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

// 注册分类相关
Route::rule('/cate/list'                ,'index/Cate/index'               ,'GET');
Route::rule('/cate/modify'                ,'index/Cate/cateModify'               ,'POST');
Route::rule('/cate/add'                ,'index/Cate/cateAdd'               ,'POST');
Route::rule('/cate/delete'                ,'index/Cate/cateDelete'               ,'POST');
Route::rule('/cate/info/:id'                ,'index/Cate/cateItem'               ,'GET');

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

// 房间部分
Route::rule('/room/list'        ,'index/Index/roomList'                ,'GET');
Route::rule('/room/disable'        ,'index/Index/disableRoom'                ,'POST');
Route::rule('/room/disable/open'        ,'index/Index/openDisableRoom'                ,'POST');
Route::rule('/room/info/:id'        ,'index/Index/roomItem'                ,'GET');
Route::rule('/room/check/list'        ,'index/Index/roomCheckList'                ,'GET');
Route::rule('/room/check/success'        ,'index/Index/roomCheckSuccess'                ,'POST');
Route::rule('/room/check/fail'        ,'index/Index/roomCheckFail'                ,'POST');
Route::rule('/room/tipoff/list'        ,'index/Index/roomTipoffList'                ,'GET');
Route::rule('/room/cate/modify'        ,'index/Index/roomCateModify'                ,'POST');

// 用户部分
Route::rule('/user/list'       ,'index/User/index'                ,'GET');
Route::rule('/user/disable'       ,'index/User/disableList'                ,'GET');
Route::rule('/user/disable'       ,'index/User/userDisable'                ,'POST');
Route::rule('/user/info/:id'       ,'index/User/userItem'                ,'GET');

// 反馈建议列表
Route::rule('/suggest/list'       ,'index/Index/suggestList'                ,'GET');
Route::rule('/suggest/delete'       ,'index/Index/suggestDelete'                ,'POST');

// 管理员
Route::rule('/admin/info'       ,'index/Admin/index'                ,'GET');
Route::rule('/admin/info/modify'       ,'index/Admin/modify'                ,'POST');

