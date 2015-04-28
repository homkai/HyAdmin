<?php
date_default_timezone_set ( 'PRC' );
/**
 * 时间戳 处理
 *
 * @param number $time
 * @param string $format
 * @param number $type
 * @return string
 */
function to_time($time, $type = 1, $format='') {
	if(!$time) return "";
	if($format) return date($format, $time);
	switch ($type) {
		case 1:
			return date('Y-m-d', $time);
		case 2:
			return date('Y-m-d H:i', $time);
		case 3:
			if(date('d', $time) == date('d'))
				return date ( '今天 H:i', $time);
			return date ( 'Y-m-d H:i', $time);
		case 4:
			$tmp = date('d', $time) == date('d') ? date('今天 h:i ', $time) : date ( 'Y-m-d ', $time );
			$hour = date ( 'H', $time );
			if($hour < 5)
				$hour = "深夜";
			elseif($hour < 7)
				$hour = "清晨";
			elseif($hour < 13)
				$hour = "上午";
			elseif($hour < 19)
				$hour = "下午";
			else
				$hour = "晚上";
			return $tmp. $hour . date (' g:i ', $time);
		case 5:	
			$count = TIME - $time;
			if($count < 60){
				return '刚刚';
			}elseif(($count/60) < 60){
				return intval($count/60).'分钟前';
			}elseif(($count/3600) < 24){
				return intval($count/3600).'小时前';
			}elseif(($count/3600/24) < 3){
				return intval($count/(3600*24)).'天前';
			}
			return date('Y-m-d',$time);
	}
}
/**
 * 令牌生成器
 * @param string $value
 * @return string
 */
function token_builder($value=false){
	session('hyToken', null);
	$token = strtr(md5(rand(10000000, 99999999)), 'ajfasw', 'homyit');
	if($value) session($token, $value);
	else session('hyToken',$token);
	return $token;
}
/**
 * 令牌校验器
 * @param string $key
 * @return boolean|string
 */
function token_validator($key=false){
	if(!$key) {
		$token=session('hyToken');
		session('hyToken',null);
		return $token ?: false;
	}
	return session("?{$key}") ? session($key) : false;
}
/**
 * URL方法参数解析
 */
function u_parser($url='',$vars='') {
	// 解析URL
	$info   =  	parse_url($url);
	$url	=	$info['path'];
	// 解析参数
	if(is_string($vars)) { // aaa=1&bbb=2 转换成数组
		parse_str($vars,$vars);
	}elseif(!is_array($vars)){
		$vars = array();
	}
	if(isset($info['query'])) { // 解析地址里面参数 合并到vars
		parse_str($info['query'],$params);
		$vars = array_merge($params,$vars);
	}
	// URL组装
	$depr       =   C('URL_PATHINFO_DEPR');
	$urlCase    =   C('URL_CASE_INSENSITIVE');
	if($url) {
		if(0=== strpos($url,'/')) {// 定义路由
			$route      =   true;
			$url        =   substr($url,1);
			if('/' != $depr) {
				$url    =   str_replace('/',$depr,$url);
			}
		}else{
			if('/' != $depr) { // 安全替换
				$url    =   str_replace('/',$depr,$url);
			}
			// 解析模块、控制器和操作
			$url        =   trim($url,$depr);
			$path       =   explode($depr,$url);
			$var        =   array();
			$varModule      =   C('VAR_MODULE');
			$varController  =   C('VAR_CONTROLLER');
			$varAction      =   C('VAR_ACTION');
			$var[$varAction]       =   array_pop($path);
			$var[$varController]   =   array_pop($path);
			if($maps = C('URL_ACTION_MAP')) {
				if(isset($maps[strtolower($var[$varController])])) {
					$maps=$maps[strtolower($var[$varController])];
					if($action = array_search(strtolower($var[$varAction]),$maps)){
						$var[$varAction] = $action;
					}
				}
			}
			if($maps = C('URL_CONTROLLER_MAP')) {
				if($controller = array_search(strtolower($var[$varController]),$maps)){
					$var[$varController] = $controller;
				}
			}
			if($urlCase) {
				$var[$varController]   =   parse_name($var[$varController]);
			}
			$module =   '';
			if(!empty($path)) {
				$var[$varModule]    =   implode($depr,$path);
			}else{
				if(C('MULTI_MODULE')) {
					if(MODULE_NAME != C('DEFAULT_MODULE') || !C('MODULE_ALLOW_LIST')){
						$var[$varModule]=   MODULE_NAME;
					}
				}
			}
			if($maps = C('URL_MODULE_MAP')) {
				if($_module = array_search(strtolower($var[$varModule]),$maps)){
					$var[$varModule] = $_module;
				}
			}
		}
	}
	return array_merge($vars,$var);
}
/**
 * 二维数组转关联数组
 * @param array $data
 * @param string $key
 * @param string|ascArr $value
 * @param string $key_postfix
 * @return ascArr
 */
function md_arr_2_asc_arr($data=array(), $key='', $value='', $key_postfix=''){
	if(!$data || !is_array($data) || !$key || !$value || !$data[0][$key]) return array();
	$arr = array();
	foreach($data as $k=>$v){
		if(is_array($value)){
			$tmp = '';
			foreach ($value as $k1=>$v1){
				if(!$v[$k1]) continue;
				$tmp.=$v1.$v[$k1];
			}
			$arr[$v[$key].$key_postfix] = $tmp;
		}
		else
			$arr[$v[$key].$key_postfix] = $v[$value];
	}
	return $arr;
}
/**
 * 二维数组转索引数组
 * @param array $data
 * @param string $key
 * @return idxArr
 */
function md_arr_2_idx_arr($data, $key){
	if(!is_array($data)) return array();
	$arr = array();
	foreach ($data as $v){
		$arr[] = $v[$key];
	}
	return $arr;
}
/**
 * 二维数组转逗号分隔字符串
 * @param array $data
 * @param string $key
 * @return string
 */
function md_arr_2_ids($data, $key){
	if(!is_array($data)) return '';
	$ids = array();
	foreach ($data as $v){
		if($v[$key]) $ids[] = $v[$key];
	}
	return implode(',', $ids);
}
/**
 * 简单数组转键值对数组
 *
 * @param idxArr $array
 * @return ascArr
 */
function idx_arr_2_asc_arr($arr = array()) {
	$kv = array();
	foreach($arr as $v) {
		$kv[$v] = $v;
	}
	return $kv;
}
/**
 * 可用于URL传递的base64编码
 * @param string $data
 * @return string
 */
function base64url_encode($data) {
	return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
/**
 * 可用于URL传递的base64解码
 * @param string $data
 * @return string
 */
function base64url_decode($data) {
	return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}
/**
 * 加密
 * @param string $encrypt
 * @param string $mc_key
 * @return string
 */
function aes_encrypt($input, $key) {
	$encryptor=new Common\Model\HyCryptoModel($key);
	return $encryptor->encrypt($input);
}
/**
 * 解密
 * @param string $decoded
 * @param string $mc_key
 * @return string
 */
function aes_decrypt($input, $key) {
	$encryptor=new Common\Model\HyCryptoModel($key);
	return $encryptor->decrypt($input);
}
/**
 * 基础AES解密
 * @param string $text
 * @param string $key
 * @return string
 */
function aes_decrypt_base($text,$key){
	$rand=substr($text, -7);
	$text=substr($text, 0, -7);
	return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($text), MCRYPT_MODE_CBC, substr($key, 5, 9).$rand), "\0");
}
/**
 * 拼接字符串
 */
function concat($num){
	$arr=func_get_args();
	array_shift($arr);
	if(!$num) return implode('', $arr);
	return implode('', array_slice($arr, 0, $num));
}
function is_in_array($test,$arr){
	if(!is_array($test)) $test=explode(',', $test);
	if(!is_array($arr)) $arr=explode(',', $arr);
	return !array_diff($test, $arr);
}
/**
 * 登录用户UID
 * @return number
 */
function ss_uid(){
	return session('userId');
}
/**
 * 用户所属学院id
 * @return number
 */
function ss_clgid(){
	return session('collegeId');
}
/**
 * 登录学生所在班级id
 * @return number
 */
function ss_clsid(){
	return session('classId');
}
/**
 * 指定用户的头像
 * @param string $id
 * @return string
 */
function avatar_file($id=''){
	$default = C('DEFAULT_AVATAR_IMG');
	if(!$id) return $default;
	return D('Common/HyFile')->download(val_decrypt($id),true) ?: $default;
}
function file_down_url($id='',$default=''){
	if(!$id) return $default?:'';
	return U('HyFile/download',array('id'=>file_id_encrypt($id)));
}
function file_id_encrypt($id){
	if(!$id) return '';
	return 'encrypt_file-'.aes_encrypt(session_id().$id,C('CRYPT_KEY_FILE'));
}
function file_id_decrypt($id=''){
	if(!$id) return '';
	return str_replace(session_id(), '', aes_decrypt(substr($id,strlen('encrypt_file-')),C('CRYPT_KEY_FILE')));
}
function act_encrypt($id){
	if(!$id) return '';
	return 'encrypt_act-'.aes_encrypt(session_id().$id,C('CRYPT_KEY_ACT'));
}
function act_decrypt($id=''){
	if(!$id) return '';
	return str_replace(session_id(), '', aes_decrypt(substr($id,strlen('encrypt_act-')),C('CRYPT_KEY_ACT')));
}
function val_encrypt($val){
	if(!$val) return '';
	$encryptor=new Common\Model\HyCryptoModel(C('CRYPT_KEY_VAL'));
	return 'encrypt_val-'.$encryptor->encrypt_fixed($val);
}
function val_decrypt($val=''){
	if(!$val) return '';
	if(false===strpos($val, 'encrypt_val-')) return $val;
	$encryptor=new Common\Model\HyCryptoModel(C('CRYPT_KEY_VAL'));
	return $encryptor->decrypt_fixed(substr($val,strlen('encrypt_val-')),C('CRYPT_KEY_VAL'));
}
function val_decrypt_quote(&$val=''){
	if(!$val) return;
	if(false===strpos($val, 'encrypt_val-')) return;
	$encryptor=new Common\Model\HyCryptoModel(C('CRYPT_KEY_VAL'));
	$val = $encryptor->decrypt_fixed(substr($val,strlen('encrypt_val-')),C('CRYPT_KEY_VAL'));
}
/**
 * Fix array_merge
 */
function array_merge_fix($arr1,$arr2){
	if(is_array($arr2))
		foreach ($arr2 as $k=>$v){
		$arr1[$k]=$v;
	}
	return $arr1;
}
function count_str_time($time){
	$time = strtotime($time);
	if($time) return to_time($time,5);
}