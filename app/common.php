<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function go_curl($url, $type, $data = false, &$err_msg = null, $timeout = 20, $cert_info = array(),$proxy = "",$cookie = "recookie.txt")
{
    $type = strtoupper($type);
    if ($type == 'GET' && is_array($data)) {
        $data = http_build_query($data);
    }
    $option = array();
    if ( $type == 'POST' ) {
        $option[CURLOPT_POST] = 1;
    }
    if ($data) {
        if ($type == 'POST') {
            $option[CURLOPT_POSTFIELDS] = $data;
        } elseif ($type == 'GET') {
            $url = strpos($url, '?') !== false ? $url.'&'.$data :  $url.'?'.$data;
        }
    }
    $option[CURLOPT_URL]            = $url;
    $option[CURLOPT_FOLLOWLOCATION] = TRUE;
    $option[CURLOPT_MAXREDIRS]      = 4;
    $option[CURLOPT_RETURNTRANSFER] = TRUE;
    $option[CURLOPT_TIMEOUT]        = $timeout;
    $option[CURLINFO_HEADER_OUT] = TRUE;
    $option[CURLOPT_HEADER] = TRUE;
    //设置证书信息
    if(!empty($cert_info) && !empty($cert_info['cert_file'])) {
        $option[CURLOPT_SSLCERT]       = $cert_info['cert_file'];
        $option[CURLOPT_SSLCERTPASSWD] = $cert_info['cert_pass'];
        $option[CURLOPT_SSLCERTTYPE]   = $cert_info['cert_type'];
    }
    //设置CA
    if(!empty($cert_info['ca_file'])) {
        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
        $option[CURLOPT_SSL_VERIFYPEER] = 1;
        $option[CURLOPT_CAINFO] = $cert_info['ca_file'];
    } else {
        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
        $option[CURLOPT_SSL_VERIFYPEER] = 0;
    }

    if(!empty($proxy)){
        $proxy_arr = explode(":",$proxy);
        $option[CURLOPT_PROXYAUTH]   = CURLAUTH_BASIC;
        $option[CURLOPT_PROXYTYPE]   = CURLPROXY_HTTP;
        $option[CURLOPT_PROXY]   = $proxy_arr[0];
        $option[CURLOPT_PROXYPORT]   = $proxy_arr[1];
        //$option[CURLOPT_PROXYUSERPWD]   = "user:pass";
    }

    if(!empty($cookie)){
        if (is_file($cookie)){
            $option[CURLOPT_COOKIEFILE]   = $cookie;
        }else{
            $option[CURLOPT_COOKIE]   = $cookie;
        }
        $option[CURLOPT_COOKIEJAR]   = "recookie.txt";
    }
    
    //user-agent
    $option[CURLOPT_USERAGENT]   = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';

    $ch = curl_init();
    curl_setopt_array($ch, $option);
    $responsebody = curl_exec($ch);
    $curl_no  = curl_errno($ch);
    $curl_err = curl_error($ch);
    $response = [];
    $response["RequestHeader"] = curl_getinfo($ch,CURLINFO_HEADER_OUT);
    $response["ResponseHeader"] = substr($responsebody,0,curl_getinfo($ch,CURLINFO_HEADER_SIZE));
    $response["Body"] = substr($responsebody,curl_getinfo($ch,CURLINFO_HEADER_SIZE));
    $response["Code"] =  curl_getinfo($ch,CURLINFO_HTTP_CODE);
    curl_close($ch);
    // error_log
    if($curl_no > 0) {
        if($err_msg !== null) {
            $err_msg = '('.$curl_no.')'.$curl_err;
        }
    }
    return $response;
}
