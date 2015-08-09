<?php
namespace System\Model;
use Common\Model\HyAllModel;

/**
 * 用户编辑模型
 *
 * @author Homkai QQ:345887894
 */
class UserModel extends HyAllModel {
	
	protected $tableName = 'user';
	
	/**
	 * @overrides
	 */
	protected function initInfoOptions() {
	}
	/**
	 * @overrides
	 */
	protected function initSqlOptions() {
		return array(
				'decrypt'=>true
		);
	}
	/**
	 * @overrides
	 */
	protected function initPageOptions() {
	}
	/**
	 * @overrides
	 */
	protected function initFieldsOptions() {
	}
	/**
	 * 写入回调 - 密码加密
	 * @param string $str
	 * @return string | boolean
	 */
	protected function callback_pwdEncrypt($str){
		if(is_string($str) && ''!==trim($str)) return D('HyAccount')->pwdEncrypt($str);
		return false;
	}	
	/**
	 * 个人信息修改入口
	 * @param array $json
	 */
	 public function getBaseInfoFields(){
		return array(
    				'phone'=>array('name'=>'电话','tip'=>'请输入11个数字'),
    				'qq'=>array('name'=>'QQ号'),
    				'email'=>array('name'=>'电子邮箱')
		);
	}
	public function ajax_updateMe(&$json){
		$data['building']=I('post.building');
		$data['bank_card']=I('post.bank_card');
		$data['room']=I('post.room');
		$data['parent_phone']=I('post.parent_phone');
		$data['home_address']=I('post.home_address');
		M('student')->where(array('user_id'=>ss_uid()))->save($data);
		unset($_POST['building'],$_POST['room'],$_POST['bank_card'],$_POST['parent_phone']);
		foreach ($this->getBaseInfoFields() as $k=>$v) $this->updateFields[]=$k;
		$_POST['phone']=val_encrypt(I('post.phone'));
		$_POST['parent_phone']=val_encrypt(I('post.parent_phone'));
		$json['status']=!!$this->update(ss_uid());
		$json['info']=($json['status'] ? '信息编辑成功！' : ($this->getError() ?: '信息编辑失败！'));
	}
	/**
	 * 个人照片修改入口
	 * @param array $json
	 */
	public function ajax_avatar(&$json){
		$this->updateFields[]='avatar_file';
		$_POST['avatar_file']=val_encrypt(token_validator(I('avatar_file')));
		$json['status']=!!$this->update(ss_uid());
		if($json['status']){
			session('avatarFile',avatar_file($_POST['avatar_file']));
			$json['reload']=true;
			return $json['info']='头像修改成功！';
		}
		$json['info']=($this->getError() ?: '头像修改失败！');
	}
	public function ajax_roleDefault(&$json){
		$this->updateFields[]='roles';
		$allRole=session('roleSwitch');
		$new=$allRole[$tmp=I('role')];
		if(!in_array($tmp, array_keys(session('roleSwitch')))) {
			$json['info']='请勿非法操作，拉黑后果自负！';
			return false;
		}
		$role=act_decrypt($tmp);
		$roles=$role;
		if(is_array($arr=session('roleIdArr'))){
			foreach ($arr as $k=>$v)
				if($v!=$role) $roles.=','.$v;
		}
		$_POST['roles']=$roles;
		$json['status']=!!$this->update(ss_uid());
		if($json['status']) {
			session('roleDefault',$role);
			$json['data']=$new;
		}
		$json['info']=($json['status'] ? '默认角色修改成功！' : ($this->getError() ?: '默认角色修改失败！'));
	}
    /**
     * 个人密码修改入口
     * @param array $json
     */
    public function ajax_pwdMe(&$json){
        $aM=new HyAccountModel();
        $this->updateFields[]='password';
        $p = $aM->pwdDecrypt($this->getFieldById(ss_uid(),'password'));
        $key = substr($p, 5, 32);
        $true = aes_decrypt_base(I('p'), $key);
        if($true!=$p){
            $json['info']='旧密码输入有误！';
            return false;
        }
        $_POST['password']=$aM->pwdEncrypt(trim(I('password')));
        $json['status']=!!$this->update(ss_uid());
        $json['info']=($json['status'] ? '密码修改成功！' : ($this->getError() ?: '密码修改失败！'));
    }
	/**
	 * 更新roles授权
	 */
	public function allowUpdateRoles(){
		$this->updateFields[]='roles';
	}
    /**
     * 检索 - 自动完成
     */
    public function ajax_serachTypeahead(&$json){
    	$val=trim(I('filter'));
    	if(!preg_match('/^([\x{4e00}-\x{9fa5}]+|\d{10})$/u', $val)) die;
    	$collegeId=ss_clgid();
    	$lists=$this->query("SELECT hy.name,hy.user_no AS `no`,class.name AS `className` FROM `stusys_user` `hy` INNER JOIN `stusys_student` AS `student` ON hy.id = student.user_id INNER JOIN `stusys_class` AS `class` ON student.class_id = class.id WHERE ( hy.name LIKE '%{$val}%' OR hy.user_no='{$val}' ) AND hy.status = 1 AND class.college_id={$collegeId} ORDER BY class.grade desc LIMIT 6");
    	if(!$lists) die;
    	$json=$lists;
    }
	
	 public function search($pk){
    	$map['status']=1;
    	if($val=act_decrypt(I('pk'))) $map['id']=$val;
    	if($val=I('query')) {
    		$map['_string']="(hy.name LIKE '%{$val}%' OR hy.user_no='{$val}')";
    	}
    	if($val=I('cls')) $map['student.class_id']=$val;
    	$map = count($map)>1 ? $map : 'false';
    	$lists=$this->where($map)->reflect(array('student|id|user_id|class_id,building,room,bank_card,parent_phone','class|student.class_id|id|name class_name'))->select('hy');
    	if(1===count($lists)){
    		$user=$lists[0];
    		$user['id']=act_encrypt($user['id']);
    		$user['name']=($user['name']);
    		$user['phone']=val_decrypt($user['phone']);
    		$user['class_id']=act_encrypt($classId=$user['class_id']);
    		$user['roles']=$this->callback_rolesRead($user['roles']);
    		$lists=null;
    	}else{
    		foreach ($lists as $k=>&$v){
    			$v['id']=act_encrypt($v['id']);
    		}
    	}
    	if($user['building'] && $user['room']){
    		$user['dorm']=$user['building'].' - '.$user['room'];
    		$roomMates=$this->reflect(array('student|id|user_id|building,room'))->where(array('student.building'=>$user['building'],'id'=>array('neq',act_decrypt($user['id'])),'student.room'=>$user['room']))->select('hy');
    		$roomMates=md_arr_2_asc_arr($roomMates,'name','phone');
    		$user['roomMates']='';
    		foreach ($roomMates as $k=>$v){
    			$user['roomMates'].=$k;
    			$user['roomMates'].=$v?'('.$v.')<br>':'';
    		}
    	}
    	$classMonitorId=D('StudentCadre')->getCadreUid($classId,'班长');
    	$classSecretaryId=D('StudentCadre')->getCadreUid($classId,'团支书');
    	$userM['monitor']=$this->where(array('id'=>$classMonitorId))->find()?:array();
    	$userS['secretary']=$this->where(array('id'=>$classSecretaryId))->find()?:array();
    	$user['class_id']=act_decrypt($user['class_id']);
    	$userI['instructor']=$this->reflect(array('instructor|id|teacher_id|class_id'))->where(array('instructor.class_id'=>$user['class_id']))->select(array('hy'=>true));
    	$ms=array_merge($userM,$userS,$userI);
    	foreach ($ms as $k=>$v)
    	{
    		if($k!='instructor')
    		{
    			$user[$k]=$v['name'].'('.$v['phone'].')';
    		}
    		else{
    			foreach($v as $k1=>$v1){
    				$user[$k].=$v1['name'].'('.$v1['phone'].')<br/>';
    			}
    		}
    		
    	} 
    	$arr['userInfo']=array(
    			'name'=>'姓名',
    			'user_no'=>'学号',
    			'sex'=>'性别',
    			'roles'=>'角色',
    			'class_name'=>'班级',
    			'monitor'=>'班长',
    			'secretary'=>'团支书',
    			'instructor'=>'辅导员',
    			'college'=>'学院',
    			'phone'=>'电话',
    			'email'=>'邮箱',
    			'dorm'=>'寝室',
    			'roomMates'=>'室友',
    			'nation'=>'民族',
    			'native'=>'籍贯',
    			'qq'=>'QQ',
    			'parent_phone'=>'家长电话',
    			'id_card'=>'身份证号',
    			'bank_phone'=>'银行卡号',
    			'roles'=>'职务',
    				
    	);
    	$arr['lists']=$lists;
    	$arr['user']=$user;
    	$arr['arrInfo']=array('class_name','user_no','nation','roles','native');
    	return $arr;
    	
    }
    public function getPersonalReflect(){
    	return array(
    	);
    }
    public function profile(){
    	$user=$this->reflect($this->getPersonalReflect())->where(array('id'=>ss_uid()))->find('hy');
    	 if(session('student')) {
    		$user['base']=array('角色'=>$roleNames=$this->callback_rolesRead($user['roles']),
    				'学院'=>session('collegeName'),
    				'班级'=>$user['class_id_text'],
    				'班内职务'=>$user['job'],
    				'宿舍楼'=>$user['building'],
    				'寝室号'=>$user['room'],
    				'累计登录次数'=>$user['login_times'].'次',
    		);
    	}else {
    		$user['base']=array(
    				'角色'=>$roleNames=$this->callback_rolesRead($user['roles']),
    				'学院'=>$user['college_id_text'],
    				'职务'=>$user['job'],
    				'部门'=>$user['department'],
    				'累计登录次数'=>$user['login_times'],
    		);
    	} 
    	$user['form']=$this->getBaseInfoFields();
    	$user['rolesname']=session('roleSwitch');
    	$user['roleDefault']=M('frame_role')->getFieldById(substr($arr['roles'],0, strpos($arr['roles'], ',')?:1),'title');
    	$user['logs']=M('frame_log')->where(array('user_id'=>ss_uid(),'status'=>1,'controller'=>'HyStart','action'=>'ajax'))->order('id desc')->getField('id,create_time,ip,description',5);
    	
    	return $user;
    }
}
