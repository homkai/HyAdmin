<?php 
namespace Common\Behavior;
use Think\Behavior;

/**
 * 框架行为扩展 - 记录操作日志
 * @author Homkai
 *
 */
class HyLogBehavior extends Behavior{
	
	/**
	 * 行为入口
	 * @see \Think\Behavior::run()
	 */
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