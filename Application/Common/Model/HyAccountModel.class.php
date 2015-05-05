<?php
namespace Common\Model;

/**
 * 用户账号、登录等相关处理模型
 * 
 * 在此根据自己的业务需求，完成相关方法的定义或修改
 * @author Homkai
 *
 */
class HyAccountModel extends HyFrameModel{
	
	/**
	 * 登录用户表
	 * @var string
	 */
	protected $tableName = 'user';
	
	/**
	 * 登录 
	 * @param string $account
	 * @return array
	 */
	public function login($account){
		$arr=$this->where(array('user_no'=>$account,'status'=>1))->field(true)->find();
		if($arr) $arr['password'] = $this->pwdDecrypt($arr['password']);
		return $arr;
	}
	/**
	 * 密码加密
	 * @param string $pwd
	 * @return string
	 */
	public function pwdEncrypt($pwd, $isSha1=false){
		if(!$isSha1) $pwd = sha1($pwd.C('PWD_HASH_ADDON'));
		return aes_encrypt($pwd, C('CRYPT_KEY_PWD'));
	}
	/**
	 * 密码解密
	 * @param string $pwd
	 * @return string
	 */
	public function pwdDecrypt($pwd){
		return aes_decrypt($pwd, C('CRYPT_KEY_PWD'));
	}
	/**
	 * 登录成功时触发
	 * @param array $user
	 */
	public function onLoginPass($user){
		session('collegeId', $user['college_id']);
	}
	/**
	 * 角色确定和切换时触发
	 * @param array $role
	 */
	public function onRoleSwitch($role){
		switch ($role['table']){
			case 'student':
				session('student', true);
				session('teacher', false);
				$belong=M($role['table'])->where(array('user_id'=>ss_uid()))->getField('class_id');
				session('classId', $belong);
				if('class' == $role['name']){
					session('userJob', D('StudentCadre')->getCadreJob(ss_uid()));
				}
				break;
			case 'teacher':
				session('student', false);
				session('teacher', true);
				if('instructor' == $role['name']){
					session('myClassArr', M('instructor')->where(array('teacher_id'=>ss_uid()))->getField('class_id',true));
					session('userJob',$belong['job']);
				}
				break;
		}
		session('collegeName', M('college')->getFieldById(session('collegeId'), 'name'));
	}
	/**
	 * 忘记密码 发送验证码邮件
	 * @param string $email
	 * @return boolean|number
	 */
	public function forgetPwdSendVerify($email=''){
		// 导入 PHPMailer
		Vendor('PHPMailer.PHPMailerAutoload');
		// 初始化
		$mail=new \PHPMailer();
		$mail->isSMTP();
		$mail->Host = 'smtp.qq.com';
		// 邮箱账号：
		$mail->Username = '';
		// 邮箱密码：
		$mail->Password = '';
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;
		// 写邮件
		$mail->CharSet='UTF-8';
		$mail->From = 'xg@homyit.net';
		$mail->FromName = S('SITE_ADMIN_TITLE').'运维团队';
		$mail->addAddress($email);		
		$mail->WordWrap = 50;
		$mail->isHTML(true);
		$mail->Subject = S('SITE_ADMIN_TITLE').' - 找回密码';
		$verify=rand(20000000, 99999999);
		$mail->Body    = "您申请的重置密码的验证码为 <b>{$verify}</b>";
		$mail->AltBody = '您收到的邮件来自'.S('SITE_ADMIN_TITLE').'运维团队，本邮件由系统自动发出，请勿回复，谢谢合作！';
		// 处理结果
		if(!$mail->send()) {
			return false;
		} else {
			return $verify;
		}
	}
}