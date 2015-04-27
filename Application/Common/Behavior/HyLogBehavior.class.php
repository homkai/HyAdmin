<?php 
namespace Common\Behavior;
use Think\Behavior;
/**
 * 浏览器防刷新检测
 */
class HyLogBehavior extends Behavior{
	
	public function run(&$params) {
		if(!isset($GLOBALS['logStep'])){
			$GLOBALS['logStep'] = array('description'=>'', 'sql'=>'');
			return;
		}
		if(is_string($params) || is_array($params)){
			if(is_array($params)){
				$style = $params['style'];
				$msg = $params['msg'];
				$sql = $params['sql'];
				if($msg && $style) $msg="<span class=\"{$style}\">{$msg}</span>";
			}elseif(is_string($params)){
				$msg = $params;
			}
			if($msg) $GLOBALS['logStep']['description'] .= " >> {$msg}";
			if($sql) $GLOBALS['logStep']['sql'] = $sql;
			return;
		}
		$log = array ();
		$log ['user_id'] = ss_uid();
		$log ['description'] = $GLOBALS['logStep']['description'];
		if (!$log ['user_id'] || !$log ['description']) return;
		$log['sql'] = $GLOBALS['logStep']['sql'];
		$log['module'] = CONTROLLER_NAME;
		$log['controller'] = CONTROLLER_NAME;
		$log['action'] = ACTION_NAME;
		$log['post'] = json_encode($_REQUEST, JSON_UNESCAPED_UNICODE);
		$log['url'] = __SELF__;
		$log['create_time'] = time();
		$log['ip'] = get_client_ip();
		M('frame_log')->add($log);
	}
}