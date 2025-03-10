<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2021/9/16
 * Time: 18:05
 */

namespace common\components;


class ResponseCode
{
    //定义一些返回的错误代码
    const SUCCESS = 200;
    const NOT_LOGIN = -1;
    const UNKNOWN_ERROR = -999;
    const NEED_VIP = -1001;
    const NEED_FILL_MOBILE = -1003;

    const REQUEST_ERROR = -2000;
    const INPUT_ERROR = -2001;
    const DATA_MISSING = -2002;
    const DATA_INVALID = -2003;
    const DATA_DUPLICATE = -2004;
    const REQUEST_ABUSE = -2005;

    const AUTH_FAILED = 403;

    const NEED_INIT = 408;
    //TODO 时间戳有问题返回这个
    const NEED_TIME_CHECK = 409;

}