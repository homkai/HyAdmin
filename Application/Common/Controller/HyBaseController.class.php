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
		if(in_array(ACTION_NAME, array('ajax')) || !($userId = ss_uid())) return;
		// 消息提醒
		$this->assign('hyAlerts', D('System/HyAlert')->getUnreadList());
	}
}