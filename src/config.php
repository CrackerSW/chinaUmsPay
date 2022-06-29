<?php

/**
 * 银联商务支付sdk配置
 */
return [
    'defaults' => [
        'debug' => env('YS_DEBUG', false),
        'version' => env('YS_VERSION', 'v1'),
        'app_id' => env('YS_APPID', ''),
        'app_key' => env('YS_APPKEY', ''),
        'tid' => env('YS_TID', ''),
        'mid' => env('YS_MID', ''),
        'inst_mid' => env('YS_INST_MID', ''),
        'msg_src_id' => env('YS_MSG_SRCID', ''),
        'need_token' => env('YS_NEED_TOKEN', true),
        'need_data_tag' => env('YS_NEED_DATA_TAG', true),
    ],
];