<?php
namespace System\Model;
use Common\Model\HyAllModel;

/**
 * 系统提醒模型 - 普通角色
 * @author Homkai
 *
 */
class HyAlertModel extends HyAllModel{
		
	/**
	 * 提醒类型
	 */
	protected $type=array(
			1=>'系统提醒',
			2=>'管理员消息'
	);
	
	/**
	 * @overrides
	 */
	protected function initTableName(){
		return 'frame_alert';
	}
	
	/**
	 * @overrides
	 */
	protected function initInfoOptions() {
		return array (
				'title' => '消息提醒',
				'subtitle' => '查看系统用户的通知提醒'
		);
	}
	
	/**
	 * @overrides
	 */
	protected function initSqlOptions(){
		return array (
				'associate'=>array('user|creator_id|id||`college_id`='.ss_clgid()),
				'where' => array (
						'_string'=> "to_users LIKE '%,".ss_uid().",%'",
						'status'=> 1
				)
		);
	}
	/**
	 * @overrides
	 */
	protected function initPageOptions() {
		return array (
				'deleteType'	=> 'status|9',
				'checkbox'	=> true,
				'actions' 	=> array (
						'read' => array (
								'title'=>'标记为已读'
						)
				),
				'buttons'=>false,
				'initJS'=>'HyAlert'
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
							'callback'=>array('to_time'),
						)
				),
				'type'=>array(
						'title'=>'类型',
						'list'=>array(
							'callback'=>array('type'),
							'search'=>array(
									'type'=>'select'
							)
						)
				),
				'category' => array (
						'title' => '类别',
						'list' => array (
								'search'=>array(
										'type' => 'text',
										'query'=> 'like'
								)
						)
				),
				'to_users'=>null,
				'message' => array (
						'title' => '内容',
						'list' => array (
								'callback'=>$this->getContentListCallback(),
								'search' => array (
										'type' => 'text',
										'query'=> 'like'
								)
						)
				),
				'read_users'=>array(
						'title'=>'已读',
						'list'=>array(
								'callback'=>array('isRead')
						)
				),
				'_checkbox'=>array(
						'list'=>array(
								'title'=>true,
								'callback'=>array('checkbox', '{:read_users}', '{#}')
						)
				)
		);
	}
	/**
	 * 截取固定长度字符串
	 * @return string
	 */
	protected function getContentListCallback(){
		return array('tplReplace', '{callback}'=>array('concat', 2 , '{callback}'=>array('mb_substr', 0, 30, 'UTF-8'), '...', '{#}'), C('TPL_DETAIL_BTN'), '{#}');
	}
	/**
	 * 禁用已读的checkbox
	 * @param string $read
	 * @return string
	 */
	protected function callback_checkbox($read){
		return false!==strpos($read, ','.ss_uid().',') ? '<input type="checkbox" disabled >' : '<input type="checkbox" >';
	}
	/**
	 * 已读的状态图标
	 * @param string $read
	 * @return string
	 */
	protected function callback_isRead($read){
		return $this->callback_status(false !== strpos($read, ','.ss_uid().','));
	}
	/**
	 * 提醒类型
	 * @return multitype:string
	 */
	protected function getOptions_type(){
		return $this->type;
	}
	/**
	 * 提醒类型
	 * @return multitype:string
	 */
	protected function callback_type($val){
		return $this->type[$val];
	}
	/**
	 * 详情弹窗
	 * @param number $pk
	 * @return string
	 */
	protected function detail($pk){
		$this->setRead($pk);
		$arr=$this->where(array('id'=>$pk))->find('hy');
		$arr['to_users']=trim($arr['to_users'], ',');
		$arr['read_users']=trim($arr['read_users'], ',');
		$uM=D('User');
		$map=" `id` IN ({$arr['to_users']}) ";
		switch ($arr['type']){
			case 1:
				$to=$uM->where($map)->getField('name',true)?:array();
				$to=implode('、', $to).'<br><div class="pull-right">'.count($to).'个人</div>';
				break;
			case 2:
				$to=$uM->associate(array('student|id|user_id','class|student.class_id|id|name class_name'))->group('class.id')->where(array('id'=>array('in',$arr['to_users'])))->field('id')->select('hy');
				$to=md_arr_2_idx_arr($to, 'class_name');
				$to=implode('<br>', $to).'<br><div class="pull-right">'.count($to).'个班</div>';
				break;
		}
		if($arr['read_users']){
			$map .= " AND `id` IN ({$arr['read_users']}) ";
			$read = $uM->where($map)->getField('name', true) ?: array();
		}
		$read= !$read ? '<div class="pull-right">无</div>' : implode('、', $read).'<br><div class="pull-right">'.count($read).'个人';
		return array('table'=>array(
				'table1'=>array(
						'title'=>'通知提醒',
						'icon'=>'fa-volume-up',
						'style'=>'green',
						'cols'=>'4,8',
						'value'=>array(
								'发布时间：'=>to_time($arr['create_time'],2),
								'消息类型：'=>$this->callback_type($arr['type']),
								'消息类别：'=>$arr['category'],
						)
				), 
				'table2'=>array(
						'title'=>'消息内容',
						'icon'=>'fa-file-text',
						'style'=>'yellow',
						'cols'=>'0,12',
						'value'=>array(
								''=>$arr['url'] ? '<a href="'.U($arr['url']).'">'.$arr['message'].'</a>' : $arr['message'],
						)
				), 
				'table3'=>array(
						'title'=>'接收用户',
						'icon'=>'fa-user',
						'style'=>'red',
						'cols'=>'0,12',
						'value'=>array(
								''=>$to
						)
				), 
				'table4'=>array(
						'title'=>'已读用户',
						'icon'=>'fa-check',
						'style'=>'purple',
						'cols'=>'0,12',
						'value'=>array(
								''=>$read,
						)
				), 
		));
	}
	/**
	 * 标记已读
	 */
	protected function ajax_read(&$json){
		$pkArr = I('pk');
		if(!$pkArr) return;
		$pkArr = explode(',', $pkArr);
		foreach ($pkArr as $k=>&$v){
			$v = act_decrypt($v);
		}
		$json['status'] = $this->setRead($pkArr);
		$json['info'] = ($json['status'] ? '操作成功！' : ($this->getError() ?: '操作失败！'));
	}
	/**
	 * 标记已读
	 * @param unknown $pkArr
	 * @return boolean
	 */
	protected function setRead($pkArr){
		$userId = ss_uid();
		return !!$this->where(array('status'=>1, 'id'=>array('in', $pkArr), '_string'=>"to_users LIKE '%,{$userId},%' AND ( IFNULL(`read_users`,'')='' OR `read_users` NOT LIKE '%,{$userId},%' )"))
			->save(array('read_users'=>array('exp', "CONCAT(IF(IFNULL(`read_users`,'')='',',',`read_users`),'{$userId},')")));
	}
	/**
	 * 系统通知接口
	 * @param string $category 提醒的类别，不超过6个字，简要分类
	 * @param string $message 提醒内容 不要超过50个字
	 * @param string $icon 图标 label-***,fa-***  e.g. label-success,fa-user 逗号分隔两个样式，前面的样式为颜色，后面的样式为图标
	 * @param string $url 系统链接 U方法参数形式 e.g. Student/all
	 * @param array|stringIds $toUsers 提醒接受者userId数组或逗号分隔的字符串
	 * @param string $context 提醒上下文，用于提醒的撤销使用 e.g. auditStudent_121 推荐把提醒类型和相关主键拼成一个字符串传入
	 * @return boolean
	 */
	public function sysAlert($category='', $message='', $icon='', $url='', $toUsers=array(), $context=''){
		$data = array(
				'category'	=>	$category,
				'message'	=>	$message,
				'icon'		=>	$icon,
				'url'		=>	$url,
				'to_users'	=>	$toUsers,
				'context'	=>	$context,
				'creator_id'=>	ss_uid(),
				'create_time'=>	time(),
				'type'		=>	1
		);
		foreach ($data as $v) if(!$v) return $this->setError('所有参数必填！');
		$data['to_users'] = is_array($toUsers) ? ','.implode(',', $toUsers).',' : ','.trim($toUsers, ',').',';
		$data['context'] = CONTROLLER_NAME.'_'.$context;
		return $this->add($data) ? true : false;
	}
	/**
	 * 系统通知撤销接口
	 * @param string $context 要撤销的提醒上下文
	 * @param string $multiple 如果为true，则忽略上下文冲突，强制撤销
	 * @return boolean
	 */
	public function sysAlertDel($context, $multiple=false){
		$map = array('creator_id'=>ss_uid(), 'context'=>CONTROLLER_NAME.'_'.$context);
		$count = $this->where($map)->count();
		if($count<1 || ($count>1 && !$multiple)) return false;
		return $this->where($map)->setField('status', $this->max('status') + 1) ? true : false;
	}
	/**
	 * 未读消息列表
	 * @return array
	 */
	public function getUnreadList(){
		// 因此方法执行频度较高，所以采用原生SQL
		$userId = ss_uid();
		$sql = "SELECT `id`,`category`,`icon`,`message`,`url`,`type`,`create_time` FROM `".DTP."frame_alert` where `to_users` LIKE '%,{$userId},%' AND ( IFNULL(`read_users`,'')='' OR `read_users` NOT LIKE '%,{$userId},%') AND `status`=1 ORDER BY `id` DESC";
		return $this->query($sql);
	}
}