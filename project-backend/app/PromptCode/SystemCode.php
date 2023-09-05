<?php

namespace App\PromptCode;

class SystemCode
{
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';
    const CODE_YES = 1;
    const CODE_NO = 0;

    const DELETED_NO = NULL; // 非删除状态

    const CODE_ARTICLE_PUBLISH = 1; // 草稿
    const CODE_ARTICLE_DRAFT = 2;  // 发布
    const CODE_ARTICLE_BIN = 3; // 回收

    const CODE_SUCCESS = 200;
    const CODE_PARAMETER_ERROR = 20001;
    const CODE_OTHER_ERROR = 21000;

    const CODE_BLACKLIST_ERROR = 41000;
}
