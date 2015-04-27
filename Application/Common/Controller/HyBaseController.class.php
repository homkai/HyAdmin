<?php
namespace Common\Controller;
use Think\Controller;
/**
 * 模块基础控制器
 * @author Homkai
 *
 */
class HyBaseController extends HyFrameController {
	
	protected function _initialize(){
		// 消息提醒
		$userId = ss_uid();
		if(!$userId) return;
		$college_id = ss_clgid();
		$alerts = M()->query("SELECT `id`,`category`,`icon`,`message`,`url`,`type`,`create_time` FROM `".DTP."frame_alert` where CONCAT(',',to_users) LIKE '%,{$userId},%' AND ( `read_users` is NULL OR CONCAT(',',`read_users`) NOT LIKE '%,{$userId},%') AND `status`=1 ORDER BY `id` DESC");
		$this->assign('hyAlerts', $alerts);
	}
}