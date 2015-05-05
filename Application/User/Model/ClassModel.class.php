<?php
namespace User\Model;
use Common\Model\HyAllModel;

/**
 * 班级管理模型
 *
 * @author Homkai QQ:345887894
 */
class ClassModel extends HyAllModel {

	/**
	 * @overrides
	 */
	protected function initTableName(){
		return 'class';
	}
	
	/**
	 * @overrides
	 */
	protected function initInfoOptions() {
		return array (
				'title' => '班级',
				'subtitle' => '管理学院本科生各年级的班级' 
		);
	}
	
	/**
	 * @overrides
	 */
	protected function initSqlOptions() {
		return array (
				'where' => array (
						'college_id' => ss_clgid (),
						'status'=>array('eq',1),
						'id'=>array('gt',10000)
				) 
		);
	}
	/**
	 * @overrides
	 */
	protected function initPageOptions() {
		return array (
				'checkbox'	=> true,
				'deleteType'=> 'status|9',
				'actions' 	=> array (
						'edit' => array (
								'title' => '编辑',
								'max' => 1 
						),
						'delete' => array (
								'title' => '删除',								
								// 是否需要确认
								'confirm' => true 
						) 
				),
				'buttons'	=> array(
						'chart' =>array(
								'title'=>'汇总',
								'icon'=>'fa-bar-chart',
								'detail'=>true
						),
						'add'=>array(
								'title'=>'新增',
								'icon'=>'fa-plus'
						)
				),
				'initJS'	=> 'ClassInfo',
		);
	}
	/**
	 * @overrides
	 */
	protected function initFieldsOptions() {
		return array (
				'name' => array (
						'title' => '名称',
						'list' => array (
								'orderdir' => 'CONVERT(name USING gbk)',
								'callback'=>array('tplReplace', C('TPL_DETAIL_BTN')),
								'search' => array (
										'query' => 'like' 
								) 
						),
						'form' => array (
								'validate' => array (
										'required' => true,
										'minlength' => 2
								)
						) 
				),
				'grade' => array (
						'title' => '年级',
						'list' => array (
								'search' => array (
										'type' => 'select' 
								)
						),
						'form' => array (
								'type' => 'text',
								'validate' => array (
										'required' => true,
										'number'=>true
								) 
						) 
				),
				'graduate' => array (
						'title' => '毕业年份',
						'list'=>array(
								'order'=>false
						),
						'form' => array (
								'type' => 'text',
								'validate' => array (
										'required' => true ,
										'number'=>true
								) 
						) 
				),
				'remark' => array (
						'title' => '备注',
						'list'=>array(
								'hidden'=>true
						),
						'form' => array (
								'type' => 'textarea' 
						) 
				),
				'college_id'=>array(
						'form' => array (
							'fill'=>array(
									'both'=>array('value',ss_clgid())
							)
						) 
				)
		);
	}
	/**
	 * 用于支持fieldsOptions
	 */
	protected function getOptions_grade() {
		return M('class')->where (array('college_id'=>ss_clgid()))->getField('grade,grade as grade1');
	}
	public function detail($pk){
		$where = array('id'=>$pk);
		$arr = $this->where($where)->find();
		$total = $this->associate(array('student|id|class_id'))->where($where)->count();
		return array('table'=>array(
				'base'=>array(
						'title'=>'班级信息',
						'icon'=>'fa-users',
						'style'=>'green',
						'value'=>array(
								'名称：'=>$arr['name'],
								'年级 ：'=>$arr['grade'],
								'毕业年份：'=>$arr['graduate'],
								'备注：'=>$arr['remark'],
						)
				),
				'student'=>array(
						'title'=>'学生信息',
						'icon'=>'fa-pencil',
						'style'=>'purple-plum',
						'value'=> array(
								'总人数：'=>$total.'人',
								'花名册：'=>'<a href="'.U('User/Student/all',array('class_id_text'=>$arr['id'])).'" class="btn default red-stripe pull-right">查看班级花名册</a>'
						)
				)
		));
	}
	/**
	 * 图表汇总
	 * @return json
	 */
	protected function detail_chart(){
		$grades=$this->associate(array('student|id|class_id'))
			->where(array('student.status'=>1,'status'=>1,'college_id'=>ss_clgid()))
			->field(array('grade'=>'name','count(grade)'=>'value'))->group('grade')->order('grade asc')->select();
		$classes=$this->associate(array('student|id|class_id'))
			->where(array('student.status'=>1,'status'=>1,'college_id'=>ss_clgid()))
			->field(array('name','count(hy.id)'=>'value'))->group('hy.id')->order('grade asc')->select();
		return array('json'=>json_encode(array('grades'=>$grades,'classes'=>$classes)));
	}
}
