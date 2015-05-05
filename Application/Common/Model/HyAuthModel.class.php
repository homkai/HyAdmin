<?php
namespace Common\Model;
use Think\Model;

/**
 * 框架菜单与权限验证模型
 * @author Homkai
 *
 */
class HyAuthModel extends HyFrameModel {
	
	protected $tableName = 'frame_action';

	/**
	 * 网站访问基础身份认证 - 严格保护模式
	 * @todo 暂时废弃
	 */
	public static function baseAccessAuth(){
		return array('status'=>true);
		self::preAuth();
		// 一次页面重载 ，只验证一次
		static $pass = false;
		if($pass) return array('status'=>true);
		array('info'=>'', 'status'=>false, 'url'=>U('System/HyStart/login'));
		$test = $_SERVER['HTTP_USER_AGENT'] === session('USER_AGENT');
		if(!$test) return array('info'=>'检测到网络环境异常，为了保障您的信息安全，请重新登录！<br>请勿在登录系统期间清空缓存或切换浏览器模式！', 'status'=>false,'url'=>U('HyStart/login'), 'time'=>5);
		$key = session('HOMYIT_BASE_AUTH_SEED');
		$counter = session('HOMYIT_BASE_AUTH_COUNTER') + 1;
		$text = $_COOKIE['_homyit_token_'];
		$decode = aes_decrypt_base($text, $key);
		if(preg_match('/Homyit(\d+)#/', $decode, $m)){
			if($m[1]>=$counter){
				session('HOMYIT_BASE_AUTH_COUNTER',$m[1]);
				return array('status'=>true);
			}
		}
		return array('info'=>'身份认证异常！为了保障您的信息安全，请重新登录！', 'status'=>false, 'url'=>U('System/HyStart/login'), 'time'=>3);
	}

	/**
	 * 权限忽略规则
	 * @return boolean
	 */
	public static function preAuth($allowPublic = false){
		// 跳过公共控制器
		if('HyStart'!==CONTROLLER_NAME && !ss_uid()){
			redirect(U('System/HyStart/login'));
		}
		if(!$allowPublic) return;
		$public = C('PUBLIC_CONTROLLER');
		return in_array(CONTROLLER_NAME, is_array($public) ? $public: explode(',', $public));
	}
	
	/**
	 * URL验证
	 * 匹配优先级说明：有参的规则比无参的优先级高；同是有参的规则，rank决定优先级
	 * @return boolean
	 */
	public static function authAccess(){
		if(self::preAuth(true)) return array('status'=>true);
		$pass = false;
		$model = null;
		$auth = S('authCache_'.session('roles'));
// 		dump($auth);
		$key = MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
		if(!$_auth = $auth[$key]) return array('status'=>false, 'info'=>'权限验证失败：当前操作无权访问！');
		$get = I('get.');
		unset($get[C('VAR_MODULE')]);
		$i = 0;
		$total = count($_auth);
		foreach ($_auth as $v){
			if($pass) break;
			if(!$v['args'] && !$get){
				$pass = true;
				$model = $v['model'];
			}
			if(!$v['args'] && $get){
				if(++$i < $total) continue;
				$pass = true;
				$model = $v['model'];
			}
			$i1 = 0;
			$total1 = count($v['args']);
			foreach($v['args'] as $k1=>$v1){
				if($v1 != $get[$k1]) break;
				if(++$i1 < $total1) continue;
				$pass = true;
				$model = $v['model'];
			}
		}
		if(!$model && 'all' != ACTION_NAME) $model = $auth[MODULE_NAME.'/'.CONTROLLER_NAME.'/all'][0]['model'];
		if($model && $pass) return array('status'=>true, 'model'=>$model);
		$info = !$pass ? '权限验证失败：当前URL无权访问！' : '';
		return array('status'=>$pass, 'info'=>$info);
	}
	/**
	 * 权限缓存
	 */
	private static function getAuthCache($accessArr){
		$actions = implode(',', array_keys($accessArr));
		$data = M()->query("SELECT `id`,`pid`,`type`,`title`,`name`,`options` FROM `".DTP."frame_action` WHERE `status`=1 AND `id` IN ({$actions}) ORDER BY `rank` DESC");
		if(!is_array($data)) return false;
		$menu = array();
		$i = 0;
		foreach ($data as $k=>$v){
			if(!in_array($v['type'], array('nav','menu'))) continue;
			if($v['pid']) continue;
			if(!$i++) $v['first']=true;
			$v['name'] = $v['name'] ? U($v['name']) : '';
			foreach ($data as $k1=>$v1){
				if($v1['pid'] == $v['id']){
					$v1['id'] = 'HY'.$v1['id'];
					$v1['pid'] = 'HY'.$v1['pid'];
					$v1['name'] = $v1['name'] ? U($v1['name']) : '';
					$v['children'][$v1['id']] = $v1;
				}
			}
			$v['id'] = 'HY'.$v['id'];
			$menu[$v['id']] = $v;
		}
		$auth = array();
		$m = C('VAR_MODULE');
		$c = C('VAR_CONTROLLER');
		$a = C('VAR_ACTION');
		foreach ($data as $k=>$v){
			if(!$v['name']) continue;
			$args = u_parser($v['name']);
			$key = $args[$m].'/'.$args[$c].'/'.$args[$a];
			unset ($args[$m], $args[$c], $args[$a]);
			$auth[$key][]=array('args'=>$args, 'model'=>$accessArr[$v['id']]);
		}
		return compact('auth', 'menu');
	}
	/**
	 * 递归取role_id和对应的access_id=>model，并缓存
	 * @param number $roleId
	 * @return string
	 */
	public static function cacheAccess($roleId){
		if(C('APP_DEV') || !S('roleAccess_'.$roleId)){
			static $roles, $accessArr;
			if(is_null($roles)) {
				$roles = $roleId;
				$accessArr = M('frame_access')->where("`role_id`=$roleId AND `status`=1")->getField('action_id,model') ?: array();
			} else {
				$roles .= ",$roleId";
				$accessArr = array_merge_fix((M('frame_access')->where("`role_id`=$roleId AND `status`=1")->getField('action_id,model') ?: array()), $accessArr);
			}
			if(($pid = M('frame_role')->where("`id`=$roleId AND `status`=1")->getField('pid')) > 0) self::cacheAccess($pid);
			S('roleAccess_'.$roleId, $arr = array('roles'=>rtrim($roles, ','), 'accessArr'=>$accessArr));
		}
		$arr = $arr ?: S('roleAccess_'.$roleId);
		session('roles', $arr['roles']);
		if(C('APP_DEV') || !S('authCache_'.$arr['roles'])) {
			$arr2 = self::getAuthCache($arr['accessArr']);
			S('authCache_'.$arr['roles'], $arr2['auth']);
			S('menuCache_'.$arr['roles'], $arr2['menu']);
		}
	}
}
