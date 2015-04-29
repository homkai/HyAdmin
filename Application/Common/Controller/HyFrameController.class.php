<?php
namespace Common\Controller;
use Think\Controller;
use Common\Model\HyAuthModel;
use Think\Hook;

// 数据表前缀
defined('DTP') or define('DTP', C('DB_PREFIX'));
// 当前时间戳
defined('TIME') or define('TIME', time());

/**
 * 框架基础控制器
 * @author Homkai
 *
 */
class HyFrameController extends Controller {
	
	// 默认模型
	protected $model;
	
	/**
	 * 构造优先级低于子类的 _initialize方法，支持使用$this->model
	 */
	public function __construct(){
		// 权限检查
		$auth=HyAuthModel::authAccess();
		if(!$auth['status']) E($auth['info']);
		if($auth['model']) $this->model = D($auth['model']);
		// 子类的_initialize方法
		parent::__construct();
		// 记录日志
		if(method_exists($this->model, 'getInfo')) {
			$log=$this->model->getInfo('_title');
			Hook::listen('hy_log', $log);
		}
		// 载入静态资源
		if(APP_DEBUG) $this->loadAssets();
	}
	/**
	 * 载入静态资源
	 */
	protected function loadAssets(){
		if(is_array($assets = C('LOAD_ASSETS'))){
			foreach ($assets as $k=>$v){
				foreach ($v as $k1=>$v1){
					foreach ($v1 as $k2=>$v2){
						$args = explode('/', $k2);
						if(!in_array($args[0], array(CONTROLLER_NAME, '*'))
						 || !in_array($args[1], array(ACTION_NAME, '*'))){
							unset($assets[$k][$k1][$k2]);
							continue;
						}
						if(is_array($assets[$k][$k1][$k2])){
							foreach ($assets[$k][$k1][$k2] as $k3=>$v3){
								$assets[$k][$k1][$k3] = $v3;
							}
							unset($assets[$k][$k1][$k2]);
						}
					}
				}
			}
			$this->assign('_assets', $assets);
		}
	}
	/**
	 * 基础身份认证
	 */
	protected function baseAccessAuth(){
		$auth = HyAuthModel::baseAccessAuth();
		if(!$auth['status']) $this->error($auth['info'], $auth['url'], $auth['time']);
	}
	/**
	 * 输出JSON到模板
	 * @param string $name
	 * @param array $data
	 */
	protected function jsonAssign($name, $data){
		$data = is_null($data) ? false : $data;
		$this->assign($name, json_encode($data));
	}
	/**
	 * 设置页面标题
	 * @param string $title
	 */
	protected function setPageTitle($title = ''){
		$this->assign('pageTitle', $title);
	}
}