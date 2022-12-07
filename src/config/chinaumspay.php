<?php

/**
 * 银联商务支付sdk配置
 */
return [
    'default' => [
        'debug' => env('YS_DEBUG', false),
        'version' => env('YS_VERSION', 'v1'),
        'app_id' => env('YS_APPID', ''),
        'app_key' => env('YS_APPKEY', ''),
        'tid' => env('YS_TID', ''),
        'mid' => env('YS_MID', ''),
        'mer_no' => env('YS_MER_NO', ''),
        'inst_mid' => env('YS_INST_MID', ''),
        'msg_src_id' => env('YS_MSG_SRCID', ''),
        'need_token' => env('YS_NEED_TOKEN', true),
        'need_data_tag' => env('YS_NEED_DATA_TAG', false),
        'md5_key' => env('YS_MD5_KEY', true),
        'sub_order' => env('YS_SUB_ORDER', ''),
        'sub_mer_no' => env('YS_SUB_MER_NO', ''),
        'group_id' => env('YS_GROUP_ID', ''),
        'private_key' => env('YS_PRIVATE_KEY',storage_path('cert/ums/rsa_private_dev.pfx')),
        'private_key_password' => env('YS_PRIVATE_KEY_PASSWORD',''),
        'public_key' => env('YS_PUBLIC_KEY',storage_path('cert/ums/rsa_public_dev.pfx')),
        'bank_no' => env('YS_BANK_NO',''),
    ],
];