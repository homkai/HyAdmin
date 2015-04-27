<?php
/**
 * Homkai's Service Model
 */
namespace System\Model;

class HomkaiServiceModel{
	
	/**
	 * 分组下拉框 - 班级
	 * @return array
	 */
	public static function getClassOptg($clsArr){
		$where = array('college_id'=>ss_clgid(), 'status'=>1);
		if(isset($clsArr)) $where['id']=array('in', $clsArr);
		$arr = M('class')->where($where)->order('grade asc')->getField('id,name,grade');
		$data = array();
		if(is_array($arr))
			foreach ($arr as $k=>$v){
				$data[$v['grade'].'级'][$k] = $v['name'];
			}
		return $data;
	}
	/**
	 * 用户头像
	 * @param number $id
	 * @return string
	 */
	public static function getAvatarTpl($id){
		return '<div class="img-thumbnail"><div style="width:200px;height:200px;background:url('.avatar_file($id).') no-repeat;background-size:cover;"></div></div>';
	}
}