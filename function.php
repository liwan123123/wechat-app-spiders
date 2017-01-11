<?php


function qencode($q)
{
    $q = base64_encode($q);
    $q = str_replace('+', '!', $q);
    $q = str_replace('/', '@', $q);
    return $q;
}

function qdecode($q)
{
    $q = str_replace('!', '+', $q);
    $q = str_replace('@', '/', $q);
    $q = base64_decode($q);
    return $q;
}


function huoduan_msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    //return $suffix ? $slice.'...' : $slice;
    return $slice;
}

//获取客户端IP
function get_ip()
{
    $unknown = 'unknown';
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    if (false !== strpos($ip, ',')) $ip = reset(explode(',', $ip));
    return $ip;
}

function rand_ip()
{
    $ip_long = array(
        array('607649792', '608174079'), //36.56.0.0-36.63.255.255
        array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
        array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
        array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
        array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
        array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
        array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
        array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
        array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
        array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
    );
    $rand_key = mt_rand(0, 9);
    $ip = long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
    return $ip;
}


function http_request($url, $header = array(), $post = array(), $ref = 'https://minapp.com/miniapp/')
{

    if (function_exists('curl_init')) {
        $ip = rand_ip();
        #$cookie_file = ROOT_PATH . '/cache/' . md5($ref) . '.txt';
        $ch = curl_init();
        if (count($header) == 0)
            $header = ['Host' => 'minapp.com', 'Referer' => 'https://minapp.com/miniapp/',];
        $headerArr = array();
        foreach ($header as $n => $v) {
            $headerArr[] = $n . ':' . $v;
        }
        $USER_AGENT = isset($_SERVER ['HTTP_USER_AGENT']) ? $_SERVER ['HTTP_USER_AGENT'] : 'Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $USER_AGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);
        curl_setopt($ch, CURLOPT_REFERER, $ref);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (count($post))
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        //curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        #curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        $contents = curl_exec($ch);
        curl_close($ch);
    } else {
        $contents = 'a';
    }
    return $contents;

}

function isutf8($word)
{
    if (preg_match("/^([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}/", $word) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}$/", $word) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){2,}/", $word) == true) {

        return true;

    } else {
        return false;
    }
}

function get_content_array($str, $start, $end, $option = 0)
{
    $start_h = $this->str_zz($start);
    $end_h = $this->str_zz($end);
    preg_match_all('/' . $start_h . '(.+?)' . $end_h . '/is', $str, $match);

    $count = count($match[1]);
    for ($i = 0; $i < $count; $i++) {

        if ($option == 1) {
            $arr[$i] = $match[1][$i];
        } else if ($option == 2) {
            $arr[$i] = $start . $match[1][$i];
        } else if ($option == 3) {
            $arr[$i] = $match[1][$i] . $end;
        } else {
            $arr[$i] = $start . $match[1][$i] . $end;
        }
    }
    return $arr;
}

function dir_path($path)
{
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/') $path = $path . '/';
    return $path;
}

function dir_list($path, $exts = '', $list = array())
{
    $path = dir_path($path);
    $files = glob($path . '*');
    foreach ($files as $v) {
        if (!$exts || preg_match("/\.($exts)/i", $v)) {
            //$v = iconv('GB2312','UTF-8',$v);
            $list[] = $v;
            if (is_dir($v)) {
                $list = dir_list($v, $exts, $list);
            }
        }
    }
    return $list;
}

function files_list($path, $exts = '', $list = array(), $type = 1)
{
    $path = dir_path($path);
    $files = glob($path . '*');
    foreach ($files as $k => $v) {
        if (!$exts || preg_match("/\.($exts)/i", $v)) {
            //$v = iconv('GB2312','UTF-8',$v);
            $list[$k]['path'] = $v;
            if ($type == 1)
                $list[$k]['filename'] = basename($v);
            elseif ($type == 2)
                $list[$k]['filename'] = basename($v, '.' . $exts);
            if (is_dir($v)) {
                $list[$k]['path'] = dir_list($v, $exts, $list);
            }
        }
    }
    return $list;
}

function down_img($url, $filepath)
{
    $ref = 'https://minapp.com/miniapp/';
    $header = array("Connection: Keep-Alive", "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Pragma: no-cache", "Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3", "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:29.0) Gecko/20100101 Firefox/29.0");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
    curl_setopt($ch, CURLOPT_REFERER, $ref);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    $curlinfo = curl_getinfo($ch);
    curl_close($ch);

    if ($curlinfo['http_code'] == 200) {
        if ($curlinfo['content_type'] == 'image/jpeg') {
            $exf = '.jpg';
        } else if ($curlinfo['content_type'] == 'image/png') {
            $exf = '.png';
        } else if ($curlinfo['content_type'] == 'image/gif') {
            $exf = '.gif';
        }
        $filename = md5($url) . $exf;
        if (!file_exists($filepath . $filename))
            $res = file_put_contents($filepath . $filename, $content);
        return $filename;
    } else {
        return false;
    }

}

function _pushMsg($msg, $tab = true)
{
    if (isset($_SERVER['SHELL'])) {
        echo $tab ? $msg . "\r\n" : $msg;
    } else {
        echo $html = <<<EOF
<div style="   
    color:#795548;
    text-shadow: 0.01em 0.01em 0.01em #999999;
    font-size: 18px;
    z-index:9999;
    font-family: “Lato, PingFang SC, Microsoft YaHei, sans-serif”;
    ">{$msg}</div>
EOF;
    }
}
