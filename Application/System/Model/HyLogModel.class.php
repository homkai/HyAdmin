<?php
namespace System\Model;
use Common\Model\HyAllModel;

class HyLogModel extends HyAllModel{
	
	protected $tableName = 'frame_log';
	
	/**
	 * @overrides
	 */
	protected function initInfoOptions() {
		return array (
				'title' => '操作日志',
				'subtitle' => '查看系统用户的操作日志记录'
		);
	}
	
	/**
	 * @overrides
	 */
	protected function initSqlOptions() {
		return array (
				'associate' => array (
						'user|user_id|id|name,user_no,sex,phone,login_last_time',
				),
				'where' => array (
						'user.college_id' => ss_clgid (),
						'status'=> 1
				)
		);
	}
	/**
	 * @overrides
	 */
	protected function initPageOptions() {
		return array (
				'deleteType'=>	'status|9',
				'actions' 	=>	false,
				'buttons' 	=>	false,
				'checkbox'	=>	false,
// 				'dtResponsive'=>false
		);
	}
	/**
	 * @overrides
	 */
	protected function initFieldsOptions() {
		return array (
				'create_time'=>array(
						'title'=>'时间',
						'list'=>array(
							'callback'=>array('create_time'),
						),
				),
				'name' => array (
						'title' => '姓名',
						'table'=>'user',
						'list' => array (
								'search'=>array(
										'type' => 'text',
										'query'=> 'like'
								)
						)
				),
				'user_no' => array (
						'title' => '工号/学号',
						'table'=>'user',
						'list' => array (
								'search'=>array(
										'type' => 'text',
										'query'=> 'like'
								)
						)
				),
				'description' => array (
						'title' => '描述',
						'list' => array (
								'search' => array (
										'type' => 'text',
										'query'=>'like'
								)
						)
				)
		);
	}
	protected function callback_create_time($time){
		return $this->callback_tplReplace(to_time($time,2), C('TPL_DETAIL_BTN'));
	}
	
	protected function detail($pk){
		$arr=$this->where(array('id'=>$pk))->find('hy');
		return array(
				'table1'=>array(
						'title'=>'日志记录',
						'icon'=>'fa-file-text',
						'style'=>'green',
						'cols'=>'3,9',
						'value'=>array(
								'时间：'=>to_time($arr['create_time'],2),
								'IP ：'=>$arr['ip'],
								'描述：'=>$arr['description'],
						)
				), 
				'table2'=>array(
						'title'=>'用户信息',
						'icon'=>'fa-user',
						'style'=>'blue',
						'value'=>array(
								'姓名：'=>$arr['name'],
								'性别 ：'=>$arr['sex'],
								'手机号：'=>val_decrypt($arr['phone']),
								'上次登录：'=>to_time($arr['login_last_time'],2),
						)
				), 
				'table3'=>array(
						'title'=>'操作参数',
						'icon'=>'fa-tachometer',
						'style'=>'yellow',
						'cols'=>'3,9',
						'value'=>array(
								'控制器：'=>$arr['controller'],
								'操作：'=>$arr['action'],
								'URL：'=>"<span data-text=\"{$arr['url']}\">".substr($arr['url'], 0, 38)."...</span>",
								'POST参数：'=>"<span data-text=\"{$arr['post']}\">".substr($arr['post'], 0, 38)."...</span>",
						)
				), 
		);
	}
}