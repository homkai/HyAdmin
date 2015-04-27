<?php
namespace System\Model;

/**
 * 系统提醒模型 - 管理员角色 入口
 * @author Homkai
 * 
 */
class HyAlertA1Model extends HyAlertModel{
	
	/**
	 * @overrides
	 */
	protected function initPageOptions() {
		return array_merge(parent::initPageOptions(),array(
				'buttons'=>array(
						'new'	=>	array(
								'title'	=>	'发布管理员消息',
								'icon'	=>	'fa-plus'
						)
				)
		));
	}
	
}