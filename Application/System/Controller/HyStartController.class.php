<?php
namespace System\Controller;
use Common\Model\HyAccountModel;
use Common\Model\HyAuthModel;
use Common\Controller\HyFrameController;
/**
 * 模块基础控制器
 * @author Homkai
 *
 */
class HyStartController extends HyFrameController {
		
	/**
	 * 登录入口
	 */
	public function login(){
		$this->userLogout();
		$this->cacheSiteSetting();
		session('LOGIN_KEY', md5(rand(10000000, 99999999)));
		$this->display('Common@HyFrame/login');
	}
	
	/**
	 * 站点信息缓存
	 */
	private function cacheSiteSetting(){
		if(C('APP_DEV') || !S('SITE_SETTINGS')){
			$arr = M('frame_setting')->where(array('status'=>array('lt', 9)))->getField('key,value');
			if(is_array($arr)) foreach ($arr as $k=>$v) S($k, $v);
			S('SITE_SETTINGS', true);
		}
	}
	/**
	 * 保持在线
	 */
	public function online(){
		$json['status'] = false;
		if(!ss_uid()) $this->ajaxReturn($json);
		if($try = I('keepTry')){
			$decode = aes_decrypt_base($try, session('HOMYIT_BASE_AUTH_SEED'));
			if(preg_match('/Homyit(\d+)#/', $decode, $r)){
				if($r[1] == 1) session('keepTryCounter', 1);
			}
			$times = session('keepTryCounter');
			if($times < C('SESSION_ONLINE')){
				$json['status'] = true;
				session('keepTryCounter', $times + 1);
			}
		}
		$this->ajaxReturn($json);
	}
	/**
	 * AJAX入口
	 */
	public function ajax(){
		$logStep .= "登录验证";
		$json = array('status'=>false, 'info'=>'', 'data'=>'');
		$u = aes_decrypt_base(I('u'), session('LOGIN_KEY'));
		$this->model = new HyAccountModel();
		switch(I('get.q')){
			// 登录验证
			case 'login':
				if(!$user = $this->model->login($u)) {
					$json['info'] = '账号不存在或已禁用！'.$u;
					break;
				}
				$key = substr($user['password'], 5, 32);
				$true = aes_decrypt_base(I('p'), $key);
				if($user['password'] != $true){
					$json['info'] = '输入的密码有误！';
					$logStep .= " >> <span class='text-warning'>密码错误</span>";
					break;
				}
				// 单点登录限制
				if(C('SINGLE_POINT_ONLINE') && $user['session_id'] && $user['session_id'] != session_id()){
					$lastTime = M(ltrim(C('SESSION_TABLE'), C('DB_PREFIX')))->getFieldBySession_id($user['session_id'], 'session_expire');
					if($lastTime && TIME - $lastTime < C('SESSION_OPTIONS.expire')){
						$json['info'] = '用户已经在线！如非正常退出，请稍后再试！';
						break;
					}
				}
				$logStep .= " >> <span class='text-success'>成功</span>";
				$json['info'] = '用户身份验证成功，玩命加载中...';
				$json['data'] = rand(10000000, 99999999);
				// 缓存身份认证信息
				session('USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
				session('HOMYIT_BASE_AUTH_COUNTER', $json['data']);
				session('HOMYIT_BASE_AUTH_SEED', substr(sha1($user['password'].'#'.$json['data']), 7, 32));
				// 更新登录记录
				$data['id'] = $user['id'];
				$data['login_last_time'] = time();
				$data['login_times'] = ++$user['login_times'];
				$data['session_id'] = session_id();
				$this->model->save($data);
				// 用户信息缓存
				session('userId', $user['id']);
				session('userName', $user['name']);
				session('avatarFile', avatar_file($user['avatar_file']));
				// 登录成功后置方法
				$this->model->onLoginPass($user);
				// 角色信息缓存
				$roleIdArr = array_unique(explode(',', trim($user['roles'], ',')));
				session('roleIdArr', $roleIdArr);
				$this->roleCache($roleIdArr[0]);
				$json['status'] = true;
				break;
				// 忘记密码 - 发送验证码
			case 'forgetSendVerify':
				$email = trim(I('e'));
				$user = $this->model->where(array('user_no'=>$u,'status'=>1))->find();
				if(!$user){
					$json['info'] = '账号不存在或已禁用！';
					break;
				}
				if(sha1(val_decrypt($user['email']))!=$email) {
					$logStep .= " >> <span class='text-warning'>忘记密码重置 - 邮箱验证失败！</span>";
					$json['info'] = '您输入的邮箱地址与系统中保存的不一致，如有异议可联系辅导员！';
					break;
				}
				if(!preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $user['email'])){
					$logStep .= " >> <span class='text-warning'>忘记密码重置 - 系统中的邮箱不合法！</span>";
					$json['info'] = '邮箱地址不合法！';
					break;
				}
				if(!$verify = $this->model->forgetPwdSendVerify($user['email'])) {
					$json['info'] = '邮件发送失败，请稍后重试！';
					break;
				}
				session($user['user_no'].'_forgetVerify', $verify);
				$json['status'] = true;
				$json['info'] = '邮件发送成功，请查收发送的验证码，并填入下框';
				break;
				// 忘记密码 - 重置密码
			case 'forgetRestPwd':
				$user = $this->model->where(array('user_no'=>$u, 'status'=>1))->find();
				if(!$user){
					$logStep .= " >> <span class='text-danger'>疑似攻击，已成功拦截！</span>";
					$json['info'] = '请勿非法操作！';
					break;
				}
				$verify = trim(I('v'));
				if(!$verify || $verify != session($user['user_no'].'_forgetVerify')){
					session($user['user_no'].'_forgetVerify',null);
					$logStep .= " >> <span class='text-warning'>忘记密码重置 - 邮箱验证码无效！</span>";
					$json['info'] = '您输入的验证码不正确，请重试！';
					break;
				}
				$this->model->where(array('id'=>$user['id']))->save(array('password'=>D('HyAccount')->pwdEncrypt(trim(I('p')),true)));
				$json['status'] = true;
				$json['info'] = '密码重置成功，请重新登录！';
				break;
		}
		// 登录日志
		if($user['id'])	{
			$log = array('user_id'=>$user['id'], 'controller'=>CONTROLLER_NAME, 'action'=>ACTION_NAME, 'post'=>json_encode(I('post.')), 'description'=>' >> '.$logStep, 'ip'=>get_client_ip(), 'create_time'=>time());
			M('frame_log')->add($log);
		}
		$this->ajaxReturn($json);
	}
	/**
	 * 角色信息缓存
	 * @param number $roleId
	 */
	private function roleCache($roleId){
		session('roleId', $roleId);
		$role = array();
		$roleSwitch = array();
		$tmp = M('frame_role')->where(array('status'=>1, 'id'=>array('IN', session('roleIdArr'))))->getField('id,name,title,table');
		if(is_array($tmp));
		foreach ($tmp as $k=>$v){
			if(!$v) continue;
			if($k == $roleId) {
				$role = $v;
				continue;
			}
			$roleSwitch[act_encrypt($k)] = $v['title'];
		}
		$roleSwitch[act_encrypt($roleId)] = $role['title'];
		$roleSwitch = array_reverse($roleSwitch);
		session('roleSwitch', $roleSwitch);
		session('roleTitle', $role['title']);
		// 角色切换后置方法
		D('HyAccount')->onRoleSwitch($role);
		HyAuthModel::cacheAccess($roleId);
	}
	
	/**
	 * 角色切换入口
	 */
	public function switchs(){
		if(!in_array($roleId=act_decrypt(I('role')), session('roleIdArr')))
			$this->error('操作非法！');
		$this->roleCache($roleId);
		$this->redirect('Index/index');
	}
	
	/**
	 * 用户登录信息注销
	 */
	private function userLogout(){
		if(!ss_uid()) return;
		$this->model = new HyAccountModel();
		$this->model->where(array('id'=>ss_uid()))->setField('session_id','');
		session('[destroy]');
	}
	
	/**
	 * 退出登录入口
	 */
    public function logout(){
    	$this->userLogout();
		$this->redirect('login');
    }
}