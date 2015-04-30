<?php
namespace Common\Controller;
use Think\Controller;
use Think\Think;
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
	 * 重写构造方法，添加_after_initialize方法
	 */
	public function __construct() {
		Hook::listen('action_begin',$this->config);
		//实例化视图类
		$this->view     = Think::instance('Think\View');
		//控制器初始化
		if(method_exists($this,'_initialize'))
			$this->_initialize();
		$this->_after_initialize();
	}
	/**
	 * 构造优先级高于子类的 _initialize方法
	 */
	protected function _initialize(){
		// 权限检查
		$auth=HyAuthModel::authAccess();
		if(!$auth['status']) E($auth['info']);
		if($auth['model']) $this->model = D($auth['model']);
	}
	/**
	 * 初始化后置方法
	 */
	protected function _after_initialize(){
		// 记录日志
		if(method_exists($this->model, 'getInfo')) {
			$log = $this->model->getInfo('_title');
			Hook::listen('hy_log', $log);
		}
	}
	/**
	 * 重写display方法，添加视图输出前标签位
	 */
	protected function display($templateFile='', $charset='', $contentType='', $content='', $prefix=''){
		Hook::listen('view_before', $this);
		return parent::display($templateFile, $charset, $contentType, $content, $prefix);
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
		$this->assign('_pageTitle', $title);
	}
}