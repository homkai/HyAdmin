<?php
namespace Common\Controller;
use Think\Hook;
/**
 * 框架管理页控制器
 * @author Homkai QQ:345887894
 *
 */
abstract class HyAllController extends HyFrameController {
	
	/**
	 * 自动初始化模型为控制器同名模型
	 */
	protected function _initialize(){
		parent::_initialize();
		// 自动初始化model
		if(null === $this->model) $this->model = D(CONTROLLER_NAME);
	}
		
	/**
	 * 管理列表页 （All In One） 入口
	 * 
	 * all方法响应URL路由请求
	 * 输出管理页框架和模型配置信息
	 * 供后续AJAX获取列表和生成表单
	 */
	public function all(){
    	// 基础访问认证
    	$this->baseAccessAuth();
		$log = array('msg'=>'管理列表页');
		Hook::listen('hy_log', $log);
		if(!method_exists($this->model, 'getInfo')){
			E('HyFrame Error: '.CONTROLLER_NAME.'模型定义异常！');
		}
		$info = $this->model->getInfo();
		$this->setPageTitle($this->model->getInfo('_title'));
		$this->jsonAssign('jsonBreadcrumb', $this->model->getBreadcrumb());
		$this->jsonAssign('jsonAll', $this->model->all());
		if(method_exists($this->model, 'all_middle')){
			$this->_mid = $this->model->all_middle();
			$ctrl = $this->model->getPageOptions('allMiddle') ?: CONTROLLER_NAME;
			$this->_middle = $this->fetch(T($ctrl.'/all_middle'));
		}
		if(method_exists($this->model, 'all_bottom')){
			$this->_btm = $this->model->all_bottom();
			$ctrl = $this->model->getPageOptions('allBottom') ?: CONTROLLER_NAME;
			$this->_bottom = $this->fetch(T($ctrl.'/all_bottom'));
		}
		$js = $this->model->getPageOptions('initJS');
		if(is_string($js)) $js = explode(',', $js);
		$this->initJS = $js;
		$this->display('Common@HyFrame/all');
	}
	
	/**
	 * AJAX请求统一入口
	 * 
	 * 响应前端的AJAX请求
	 * $GET['q']为请求接口
	 * 由HyAllModel::ajax()统一路由
	 * 对应的HyAllModel::ajax_$GET['q']处理该请求
	 */
	public function ajax(){
		$log = array('msg'=>'数据请求');
		Hook::listen('hy_log', $log);
		// 对detail请求返回HTML
		if(($query = I('get.q')) === 'detail'){
			return $this->detail();
		}
		$json = array('status'=>false, 'info'=>'', 'data'=>''); // 此$json全程以引用型传递！
		$this->model->ajax($query, $json);
		$log = array('msg'=>$json['info'], 'style'=>$json['status'] ? 'text-success' : 'text-warning','sql'=>$this->model->_sql());
		Hook::listen('hy_log', $log);
		$this->ajaxReturn($json);
	}
	
	/**
	 * detail请求
	 */
	private function detail(){
		$log=array('msg'=>'浏览详情');
		Hook::listen('hy_log',$log);
		$type = I('type');
		$data = $this->model->ajax_detail($type);
		if(false === $data) {
			$this->error($this->model->getError(), '#');
		}else{
			$this->data = $data;
		}
		if($type) $tpl = $this->model->getPageOptions('detailTpl_'.$type);
		if(!$tpl) $tpl = $this->model->getPageOptions('detailTpl');
		$this->display($tpl);
	}
}