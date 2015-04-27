<?php
namespace Common\Model;
use Think\Model;

class HyBaseModel extends HyFrameModel {
		
	protected function _initialize(){
	}
	protected function _after_initialize(){
	}
	/**
	 * 用于支持fieldsOptions
	 */
	protected function getOptions_grade() {
		$arr = M ( 'class' )->where ( array (
				'college_id' => ss_clgid()
		) )->getField ( 'grade', true);
		return idx_arr_2_asc_arr($arr);
	}
	/**
	 * 用于支持fieldsOptions
	 */
	protected function getOptions_student_id($clsId = 0) {
		if(!$clsId){
			if($tmp=ss_my_cls_arr()) $clsId=$tmp;
			if($tmp=ss_clsid()) $clsId=$tmp;
			if(!$clsId) return array();
		}
		$arr = D ('User')->reflect(array("student|id|user_id||`status`=1"))->where(array('student.class_id'=>array('in',$clsId)))->field(array('id','name'))->select('hy');
		return md_arr_2_asc_arr($arr,'id','name');
	}
	/**
	 * 用于支持fieldsOptions
	 */
	protected function getOptions_class_id() {
		$arr = M ( 'class' )->where ( array (
				'college_id' => ss_clgid() ,
				'status'=>1
		) )->getField ( 'id,name' );
		return $arr;
	}	
	/**
	 * 星期几 数字转换
	 * @param unknown $num
	 * @return string
	 */
	protected function callback_day($num){
		$day=array('','一','二','三','四','五','六','日');
		return '星期'.$day[$num];
	}
	/**
	 * 星期列表
	 * @return ascArr
	 */
	protected function getOptions_day(){
		$arr=array();
		for($i=1;$i<8;$i++)
			$arr[$i]=$this->callback_day($i);
			return $arr;
	}
	/**
	 * ajax 班级学生联动选择
	 * @param unknown $json
	 */
	public function ajax_select_studentViaClass(&$json){
		$id=I('id');
		$data = D('User')->reflect(array('student|id|user_id'))->where(array('student.class_id'=>$id))->select('hy');
		if(!$data) return $json;
		$json['status']=true;
		$json['data'] = md_arr_2_asc_arr($data,'id','name');
	}
	/**
	 * 角色读取回调
	 */
	public function callback_rolesRead($ids,$spliter='、'){
		return implode($spliter,M('frame_role')->where(array('status'=>1,'id'=>array('IN',$ids)))->getField('title',true));
	}
}
