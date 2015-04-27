<?php
namespace System\Model;

/**
 * 系统提醒模型 - 管理员角色 发布消息
 * @author Homkai
 *
 */
class HyAlertA2Model extends HyAlertModel{
	
	/**
	 * @overrides
	 */
	protected function initInfoOptions() {
		return array (
				'title' => '消息提醒',
				'subtitle' => '发布管理员消息'
		);
	}
	
	/**
	 * @overrides
	 */
	protected function initSqlOptions(){
		return array_merge(parent::initSqlOptions(),array (
				'where' => array (
						'type'=>array('eq', 2),
						'creator_id'=>ss_uid(),
						'status'=>array('lt', 9)
				)
		));
	}
	/**
	 * @overrides
	 */
	protected function initPageOptions() {
		return array_merge(parent::initPageOptions(),array(
				'buttons'=>array(
						'add'=>array(
								'title'=>'发布',
								'icon'=>'fa-plus'
						),
						'back'=>array(
								'title'=>'返回',
								'icon'=>'fa-history'
						),
				),
				'actions'=>array(
						'delete' => array (
								'title' => '删除',								
								// 是否需要确认
								'confirm' => true 
						)
				)
		));
	}
	/**
	 * @overrides
	 */
	protected function initFieldsOptions() {
		return array (
				'create_time'=>array(
						'list'=>array(
							'title'=>'时间',
							'callback'=>array('to_time'),
						),
						'form'=>array(
								'fill'=>array(
										'both'=>array('value', TIME)
								)
						)
				),
				'type'=>array(
						'form'=>array(
								'fill'=>array(
										'both'=>array('value', 2)
								)
						),
				),
				'category' => array (
						'title' => '类别',
						'list' => array (
								'search'=>array(
										'type' => 'text',
										'query'=>'like'
								)
						),
						'form'=>array(
								'type'=>'text',
								'tip'=>'填写通知用途，方便筛选',
								'validate' => array (
										'required' => true,
										'maxlength'=>10
								),
						)
				),
				'to_users' => array (
						'form' => array (
								'title' => '班级',
								'type' => 'select',
								'select'=>array(
										'multiple'=>true,
										'optgroup'=>true
								),
								'validate' => array (
										'required' => true
								),
								'fill'=>array(
										'both'=>array('to_users')
								)
						),
				),
				'message' => array (
						'title' => '内容',
						'list' => array (
								'callback'=>$this->getContentListCallback(),
								'search' => array (
										'type' => 'text',
										'query'=>'like'
								)
						),
						'form'=>array(
								'type'=>'textarea',
								'attr'=>'style="height:150px;"',
								'validate' => array (
										'required' => true,
										'maxlength'=> 180
								)
						)
				),
				'read_users' => null,
				'icon'=>array(
						'form'=>array(
								'fill'=>array(
										'both'=>array('value', C('ADMIN_ALERT_ICON'))
								)
						)
				),
				'creator_id'=>array(
						'form'=>array(
								'fill'=>array(
										'both'=>array('value',ss_uid())
								)
						)
				)
		);
	}
	/**
	 * 年级分组的班级
	 * @return array
	 */
	protected function getOptions_to_users(){
		return HomkaiServiceModel::getClassOptg($arr);
	}
	/**
	 * 写入班级所有同学
	 * @param array $classArr
	 * @return string
	 */
    protected function callback_to_users($classArr){
		$data = D('User/Class')->associate(array('student|id|class_id|user_id'))->where(array('status'=>1, 'id'=>array('in', $classArr)))->select('hy');
		$ids = md_arr_2_ids($data, 'user_id');
		return $ids ? $ids.',' : '';
	}
}