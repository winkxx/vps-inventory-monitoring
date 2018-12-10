<?php
return [
    'app' => [
        'addpass' => true, //公共添加 true为指定用户可添加 false 为所有人都可以添加 不过需要审核
        'adduid' => "1", //添加功能用户UID [,]分隔 如 1,3,5
        'testtick' => 600, //命令行定时执行时间 秒为单位 60*60 即3600秒
        'host' => 'https://vps.honoka.club', //这里修改成自己的域名
        'tgchannel' => 'https://t.me/luzhujichannel', //telegram 频道地址
        'tgchannelsckey' => '8009+OrTUX2oY4K/X5GfAPsFyF+S+6OCUQLo7uUfUkyk3sPvnZZmyK986w', // 邀请 https://t.me/HonokaVPS_StockBot 进入频道 设置为管理员 然后在频道发送 /start 获取sckey 填入
        'testdebug' => false, //是否输出调试信息到文件
        'captcha' => true,//启动验证码
        'bot_token' => '', //你的 TG 机器人的 token
    ],
];
