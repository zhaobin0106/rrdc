<?php
function token($length = 32, $type = 'string') {
	// Create random token
    $type = strtolower($type);
	$string = $type == 'string' ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' : '0123456789';
	
	$max = strlen($string) - 1;
	
	$token = '';
	
	for ($i = 0; $i < $length; $i++) {
		$token .= $string[mt_rand(0, $max)];
	}	
	
	return $token;
}

function getIP() {
    $ip = "unknown";
    if (getenv("HTTP_CLIENT_IP")) {
        $ip = getenv("HTTP_CLIENT_IP");
    } else if (getenv("HTTP_X_FORWARDED_FOR")) {
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    } else if (getenv("REMOTE_ADDR")) {
        $ip = getenv("REMOTE_ADDR");
    }
    return $ip;
}

function ncPriceFormat($price) {
    return number_format($price, 2, '.', '');
}

function thReplaceText($message, $param) {
    if (!is_array($param)) return false;
    foreach ($param as $k => $v) {
        $message = str_replace('{$' . $k . '}', $v, $message);
    }
    return $message;
}

/**
 * 把平坦数据组装成层级结构的数据
 * @param array $data 平坦的数据
 * @param array $root 当前根节点
 * @param string $id_key 平坦数据中节点id的下标
 * @param string $parent_key 坦数据中父节点id的下标
 * @return array 返回层级结构的数据，下标`children`指向节点的子节点数组；如果数据为空或者数据结构不对，则返回null
 */
function makeTree($data, $root, $id_key = "id", $parent_key = "parent_id") {
	$return = array();
	// 遍历所有数据，找到当前根节点的子节点
	foreach($data as $index => $node) {
		// 找到当前根节点的子节点
		if($node[$parent_key] == $root[$id_key]) {
			// 把该节点从数据中移除（已经分析过了，不再需要了）
			unset($data[$index]);
			// 以该节点作为根节点，分析剩余的数据，得到该节点的子节点数组
			$node['children'] = makeTree($data, $node, $id_key, $parent_key);
			// 把该节点当前节点的子节点数组中
			$return[] = $node;
		}
	}
	return empty($return) ? null : $return;
}

/**
 * 把树状结构数组组转成平坦数据
 * 可先用makeTree获取指定原始id的子孙数据，再用此平坦化成一维数组
 * @param $data 树状结构数组
 * @return array 平坦数组
 */
function unmakeTree($data) {
    $arr = array();
    if (is_array($data) && !empty($data)) {
        foreach ($data as $index => $node) {
            $temp = $node;
            unset($temp['children']);
            $arr[] = $temp;
            if (!empty($node['children'])) {
                $arr = array_merge($arr, unmakeTree($node['children']));
            }
        }
    }
    return empty($arr) ? null : $arr;
}

/**
 * 语言包解析
 * @param string $keyword 变量
 * @param string $type 数据类型
 * @return string 变量译文
 */
function G($keyword, $type = 'language') {
	global $registry;
	return $registry->get($type)->get($keyword);
}

/**
 * 快递100 -- 快递接口
 * @param $company string 快递公司代号
 * @param $code string 快递单号
 * @return mixed|null
 */
function express($company, $code) {
	$appKey = 'cb3d982383a2bfb6';
	$url = 'http://api.kuaidi100.com/api?id='.$appKey.'&com='.$company.'&nu='.$code.'&show=0&muti=1&order=asc';
	if (function_exists('curl_init') == 1){
		$data = array();
		$curl = curl_init();
		curl_setopt ($curl, CURLOPT_URL, $url);
		curl_setopt ($curl, CURLOPT_HEADER,0);
		curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
		curl_setopt ($curl, CURLOPT_TIMEOUT,5);
		$json = curl_exec($curl);
		curl_close ($curl);
		return json_decode($json, true);
	}
}

/**
 * getallheaders()函数：获取请求的头列表
 */
if (!function_exists('getallheaders'))
{
	function getallheaders()
	{
		$headers = '';
		foreach ($_SERVER as $name => $value)
		{
			if (substr($name, 0, 5) == 'HTTP_')
			{
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
}


/**
 * getallheaders()函数：获取请求的头列表
 */
if (!function_exists('uuid'))
{
	function uuid() {
		// fix for compatibility with 32bit architecture; seed range restricted to 62bit
		$seed = mt_rand(0, 2147483647) . '#' . mt_rand(0, 2147483647);

		// Hash the seed and convert to a byte array
		$val = md5($seed, true);
		$byte = array_values(unpack('C16', $val));

		// extract fields from byte array
		$tLo = ($byte[0] << 24) | ($byte[1] << 16) | ($byte[2] << 8) | $byte[3];
		$tMi = ($byte[4] << 8) | $byte[5];
		$tHi = ($byte[6] << 8) | $byte[7];
		$csLo = $byte[9];
		$csHi = $byte[8] & 0x3f | (1 << 7);

		// correct byte order for big edian architecture
		if (pack('L', 0x6162797A) == pack('N', 0x6162797A)) {
			$tLo = (($tLo & 0x000000ff) << 24) | (($tLo & 0x0000ff00) << 8)
				| (($tLo & 0x00ff0000) >> 8) | (($tLo & 0xff000000) >> 24);
			$tMi = (($tMi & 0x00ff) << 8) | (($tMi & 0xff00) >> 8);
			$tHi = (($tHi & 0x00ff) << 8) | (($tHi & 0xff00) >> 8);
		}

		// apply version number
		$tHi &= 0x0fff;
		$tHi |= (3 << 12);

		// cast to string
		$uuid = sprintf(
			'%08x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x',
			$tLo,
			$tMi,
			$tHi,
			$csHi,
			$csLo,
			$byte[10],
			$byte[11],
			$byte[12],
			$byte[13],
			$byte[14],
			$byte[15]
		);

		return $uuid;
	}
}

/**
 * 加密函数
 *
 * @param string $txt
 *        	需要加密的字符串
 * @param string $key
 *        	密钥
 * @return string 返回加密结果
 */
function encrypt($txt, $key = '') {
    if (empty ( $txt ))
        return $txt;
    if (empty ( $key ))
        $key = md5 ( MD5_KEY );
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
    $ikey = "-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
    $nh1 = rand ( 0, 64 );
    $nh2 = rand ( 0, 64 );
    $nh3 = rand ( 0, 64 );
    $ch1 = $chars {$nh1};
    $ch2 = $chars {$nh2};
    $ch3 = $chars {$nh3};
    $nhnum = $nh1 + $nh2 + $nh3;
    $knum = 0;
    $i = 0;
    while ( isset ( $key {$i} ) )
        $knum += ord ( $key {$i ++} );
    $mdKey = substr ( md5 ( md5 ( md5 ( $key . $ch1 ) . $ch2 . $ikey ) . $ch3 ), $nhnum % 8, $knum % 8 + 16 );
    $txt = base64_encode ( time () . '_' . $txt );
    $txt = str_replace ( array (
        '+',
        '/',
        '='
    ), array (
        '-',
        '_',
        '.'
    ), $txt );
    $tmp = '';
    $j = 0;
    $k = 0;
    $tlen = strlen ( $txt );
    $klen = strlen ( $mdKey );
    for($i = 0; $i < $tlen; $i ++) {
        $k = $k == $klen ? 0 : $k;
        $j = ($nhnum + strpos ( $chars, $txt {$i} ) + ord ( $mdKey {$k ++} )) % 64;
        $tmp .= $chars {$j};
    }
    $tmplen = strlen ( $tmp );
    $tmp = substr_replace ( $tmp, $ch3, $nh2 % ++ $tmplen, 0 );
    $tmp = substr_replace ( $tmp, $ch2, $nh1 % ++ $tmplen, 0 );
    $tmp = substr_replace ( $tmp, $ch1, $knum % ++ $tmplen, 0 );
    return $tmp;
}

/**
 * 解密函数
 *
 * @param string $txt
 *        	需要解密的字符串
 * @param string $key
 *        	密匙
 * @return string 字符串类型的返回结果
 */
function decrypt($txt, $key = '', $ttl = 0) {
    if (empty ( $txt ))
        return $txt;
    if (empty ( $key ))
        $key = md5 ( MD5_KEY );

    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
    $ikey = "-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
    $knum = 0;
    $i = 0;
    $tlen = @strlen ( $txt );
    while ( isset ( $key {$i} ) )
        $knum += ord ( $key {$i ++} );
    $ch1 = @$txt {$knum % $tlen};
    $nh1 = strpos ( $chars, $ch1 );
    $txt = @substr_replace ( $txt, '', $knum % $tlen --, 1 );
    $ch2 = @$txt {$nh1 % $tlen};
    $nh2 = @strpos ( $chars, $ch2 );
    $txt = @substr_replace ( $txt, '', $nh1 % $tlen --, 1 );
    $ch3 = @$txt {$nh2 % $tlen};
    $nh3 = @strpos ( $chars, $ch3 );
    $txt = @substr_replace ( $txt, '', $nh2 % $tlen --, 1 );
    $nhnum = $nh1 + $nh2 + $nh3;
    $mdKey = substr ( md5 ( md5 ( md5 ( $key . $ch1 ) . $ch2 . $ikey ) . $ch3 ), $nhnum % 8, $knum % 8 + 16 );
    $tmp = '';
    $j = 0;
    $k = 0;
    $tlen = @strlen ( $txt );
    $klen = @strlen ( $mdKey );
    for($i = 0; $i < $tlen; $i ++) {
        $k = $k == $klen ? 0 : $k;
        $j = strpos ( $chars, $txt {$i} ) - $nhnum - ord ( $mdKey {$k ++} );
        while ( $j < 0 )
            $j += 64;
        $tmp .= $chars {$j};
    }
    $tmp = str_replace ( array (
        '-',
        '_',
        '.'
    ), array (
        '+',
        '/',
        '='
    ), $tmp );
    $tmp = trim ( base64_decode ( $tmp ) );

    if (preg_match ( "/\d{10}_/s", substr ( $tmp, 0, 11 ) )) {
        if ($ttl > 0 && (time () - substr ( $tmp, 0, 11 ) > $ttl)) {
            $tmp = null;
        } else {
            $tmp = substr ( $tmp, 11 );
        }
    }
    return $tmp;
}

function callback($state = true, $msg = '', $data = array()) {
    return array(
        'state' => $state,
        'msg' => $msg,
        'data' => $data
    );
}

function _dealWhere($condition) {
    $where = '';
    if (($condition)) {
        if (is_array($condition)) {
            $tmpArr = array();
            foreach ($condition as $key => $value) {
                if (is_numeric($key)) {
                    $tmpArr[] = $value;
                } else {
                    if (is_array($value)) {
                        $tmpArr[] = "$key IN ('" . implode("', '", $value) . "')";
                    } else {
                        $tmpArr[] = "$key = '$value'";
                    }
                }
            }
            $where = " WHERE " . implode(' AND ', $tmpArr);
        } else {
            $where = " WHERE $where";
        }
    }
    return $where;
}

function unescape($str) {
	$ret = '';
	$len = strlen($str);
	for ($i = 0; $i < $len; $i++) {
		if ($str[$i] == '%' && $str[$i + 1] == 'u') {
			$val = hexdec(substr($str, $i + 2, 4));
			if ($val < 0x7f) {
				$ret .= chr($val);
			} else {
				if ($val < 0x800) {
					$ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f)); 
				} else {
                    $ret .= chr(0xe0 | ($val >> 12)) . 
                     chr(0x80 | (($val >> 6) & 0x3f)) . 
                     chr(0x80 | ($val & 0x3f)); 
				}			
			}
			$i += 5;
		} else {
			if ($str[$i] == '%') {
				$ret .= urldecode(substr($str, $i, 3));
				$i += 2;
			} else {
				$ret .= $str[$i];
			}
		}
	}
	return $ret;
}

function is_mobile($mobile) {
    if (preg_match("/^1[34578]{1}\d{9}$/", $mobile)) {
        return true;
    }
    return false;
}

function pass_hash($password) {
    $hash = md5($password);
    return md5(substr($hash, 16, 16) . substr($hash, 0, 16));
}

function check_submit() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        return true;
    } else {
        return false;
    }
}

/**
 * 获取静态文件地址
 */
function get_static_url($filepath) {
    return HTTP_CATALOG . $filepath;
}

/**
 * 获取url的文件名
 * @param $url
 * @return mixed
 */
function retrieve($url) {
    preg_match('/\/([^\/]+\.[a-z]+)[^\/]*$/',$url,$match);
    return $match[1];
}