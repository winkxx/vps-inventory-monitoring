# 这个项目能干什么？
通过 curl 抓取网页源代码，监控目标字段的变化, 判断 VPS 是否补货并完成 TG 频道推送。  
[Demo](http://vps.honoka.club)

## 环境说明

```
php ^ 5.5.0
```

## 简洁安装指南


1.创建数据库导入数据库文件 `mysql.sql`

2.修改数据库配置文件 `RestockPusher/app/database.example.php` 重命名为 `RestockPusher/app/database.php`

3.将 Web 服务器运行目录设置为 `RestockPusher/public`

4.修改系统配置 `RestockPusher/app/index/config.php` 定时时间 域名 等  

5.SSH 进入网站目录 运行 `php think VpsTest` 系统开始自动验证 

6.访问即可查看结果

-----

## 常见问题

1.添加页面出现404错误  ==> 设置 Url ReWrite  

2.添加后不会检测       ==> 修改数据库 `xm_index` 的 `status` 为 `1` ( `1` 视为通过审核)  

3.添加权限管理         ==> `app/index/config.php` 中的第 `5` 行，把你的 `userid` 添加进去. 

5.待更新

----
## 函数说明
 - `$curl["Code"]` 返回状态码  
 - `$curl["RequestHeader"]` 请求头  
 - `$curl["ResponseHeader"]` 返回头  
 - `$str` 返回源代码
 - `$value["stock"]` 原库存状态
### 演示检测函数
```
if ($curl["Code"] != 200){ //首先判断状态码
    return false;
}
if (strpos($str,"MineCloud")==false){ //检测是否正常打开有无公司名字之类关键词
    return $value["stock"]; //返回原库存状态
}
if (strpos($str,"缺货中")!==false){ //检测是否含有缺货关键词
    return false; 
}
return true;
```