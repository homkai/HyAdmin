<?php
namespace Common\Model;
class HyAccountModel extends HyBaseModel{
	
	protected $tableName = 'user';
	
	/**
	 * 登录 
	 * @param string $account
	 * @return kvArr
	 */
	public function login($account){
		$arr=$this->where(array('user_no'=>$account,'status'=>1))->field(true)->find();
		if($arr)$arr['password']=$this->pwdDecrypt($arr['password'],C('CRYPT_KEY_PWD'));
		return $arr;
	}
	/**
	 * 密码加密
	 * @param string $pwd
	 * @return string
	 */
	public function pwdEncrypt($pwd,$isSha1=false){
		if(!$isSha1)$pwd=sha1($pwd);
		return aes_encrypt($pwd, C('CRYPT_KEY_PWD'));
	}
	/**
	 * 密码加密
	 * @param string $pwd
	 * @return string
	 */
	public function pwdDecrypt($pwd){
		return aes_decrypt($pwd,C('CRYPT_KEY_PWD'));
	}
	
	public function forgetPwdSendVerify($email=''){
		// 导入 PHPMailer
		Vendor('PHPMailer.PHPMailerAutoload');
		// 初始化
		$mail=new \PHPMailer();
		$mail->isSMTP();
		$mail->Host = 'smtp.qq.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'xg@homyit.net';
		$mail->Password = 'homyit.2014.xg';
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;
		// 写邮件
		$mail->CharSet='UTF-8';
		$mail->From = 'xg@homyit.net';
		$mail->FromName = '宏奕-学工系统运维团队';
		$mail->addAddress($email);		
		$mail->WordWrap = 50;
		$mail->isHTML(true);
		$mail->Subject = '学生工作综合服务平台 - 找回密码';
		$verify=rand(20000000, 99999999);
		$mail->Body    = "您申请的重置密码的验证码为 <b>{$verify}</b>";
		$mail->AltBody = '您收到的邮件来自宏奕-学工系统运营团队，本邮件由系统自动发出，请勿回复，谢谢合作！';
		// 处理结果
		if(!$mail->send()) {
			return false;
		} else {
			return $verify;
		}
	}
}