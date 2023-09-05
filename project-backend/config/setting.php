<?php

ini_set('date.timezone', 'Asia/Shanghai');

return [
    'verification_code_expires_time' => date("Y-m-d H:i:s", strtotime("-10 minute")), // 验证码有效时间
    'output_verification_code_expires_time' => 10, // 显示给用户看的，验证码有效时间多少分钟
    'verification_code_sending_interval' => 30, // 发送验证码时间间隔
    'output_verification_code_sending_interval' => 50, // 显示给用户看的，发送验证码时间间隔
];
