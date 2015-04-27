<?php
namespace System\Model;
use Common\Model\HyAllModel;

/**
 * 系统公告模型
 *
 * @author Homkai
 */
class HyNoticeModel extends HyAllModel {
		
	protected $tableName = 'frame_notice';
	/**
	 * @overrides
	 */
	protected function initInfoOptions() {
		return array (
				'title' => '系统公告',
				'subtitle' => '系统面向用户发布的公告信息' 
		);
	}
	
	/**
	 * @overrides
	 */
	protected function initSqlOptions() {
		return array (
				'where' => array (
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
				'formSize'	=>	'large',
				'allScript'	=>	'HyFrame:import_umeditor',
				'initJS'	=>	'UMEditor',
		);
	}
	/**
	 * @overrides
	 */
	protected function initFieldsOptions() {
		return array (
				'title' => array (
						'title' => '标题',
						'list' => array (
								'callback'=>array('tplReplace', C('TPL_DETAIL_BTN')),
								'search' => array (
										'query' => 'like'
								)
						),
						'form' => array (
								'type' => 'text',
								'validate' => array(
									'required' =>true,
									'maxlength'=>20
								),
						)
				),
				'content'=>array(
						'title'=>'公告内容',
						'list'=>array(
								'hidden'=>true
						),
						'form'=>array(
								'type'=>'textarea',
								'attr'=>'style="height:200px;width:107%;"',
								'style'=>'make-umeditor',
								'fill'=>array(
										'both'=>array('content')
								),
								'validate' => array(
										'required' =>true
								),
						),
				),
				'file_id'=>array(
						'title' => '相关文件',
						'list'=>array(
								'callback'=>array('fileDown'),
								'order'=>false
						),
						'form'=>array(
							'type'=>'file',
							'style'=>'input-small'
						)
				),
				'create_time'=>array(
						'title'=>'发布时间',
						'list'=>array(
								'callback'=>array('to_time'),
								'search'=>array(
										'title'=>'发布时间不早于',
										'type'=>'date',
										'callback'=>array('tplReplace','{callback}'=>array('strtotime'),' create_time < ({0}+3600*24)','{#}')
								)
						),
						'form'=>array(
								'add' => false,
								'edit' => false,
								'fill'=>array(
										'add'=>array('value', time())
								)
						)
				),
				'update_time'=>array(
						'title'=>'修改时间',
						'list'=>array(
								'callback'=>array('to_time')
						),
						'form'=>array(
								'add' => false,
								'edit' => false,
								'fill'=>array(
										'edit'=>array('value', time())
								)
						)
				),
				'creator_id'=>array(
						'form'=>array(
								'fill'=>array(
										'both'=>array('value', ss_uid())
								)
						)
				),
		);
	}
	
	protected function detail($pk){
		$arr=$this->where(array('id'=>$pk))->find('hy');
		return array(
				'table1'=>array(
						'title'	=>	'通知公告',
						'icon'	=>	'fa-volume-up',
						'style'	=>	'red',
						'value'	=>	array(
								'标        题：'=>$arr['title'],
								'创建时间：'=>to_time($arr['create_time']),
								'相关文件：'=>$arr['file_id'] ? '<a href="'.file_down_url($arr['file_id']).'">下载文件</a>' : "无",
						)
				),
				'table2'=>array(
					   'title'	=>	'公告内容',
						'icon'	=>	'fa-file-text',
						'style'	=>	'purple table2',
						'cols'	=>	'0,12',
						'value'	=>	array(
								''=>$arr['content'],
						)
				),				
		);
	}
}