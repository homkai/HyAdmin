<?php
namespace Common\Controller;
use Think\Hook;
/**
 * 框架基础控制器
 * @author Homkai QQ:345887894
 *
 */
abstract class HyAllController extends HyBaseController {
	
	protected function _initialize(){
		parent::_initialize();
		// 自动初始化model
		if(null === $this->model) $this->model=D(CONTROLLER_NAME);
	}
		
	/**
	 * 管理列表 All In One 页面入口
	 */
	public function all(){
    	// 基础访问认证
    	$this->baseAccessAuth();
		$log=array('msg'=>'管理列表页');
		Hook::listen('hy_log',$log);
		if(!method_exists($this->model, 'getInfo')){
			E('HyFrame Error: '.CONTROLLER_NAME.'模型定义异常！');
		}
		$info=$this->model->getInfo();
		$this->setPageTitle($this->model->getInfo('_title'));
		$this->jsonAssign('jsonBreadcrumb', $this->model->getBreadcrumb());
		$this->jsonAssign('jsonAll', $this->model->all());
		if(method_exists($this->model, 'all_middle')){
			$this->_mid=$this->model->all_middle();
			$contl = $this->model->getPageOptions('allmiddle') ?: CONTROLLER_NAME;
			$this->_middle=$this->fetch(T($contl.'/all_middle'));
		}
		if(method_exists($this->model, 'all_bottom')){
			$this->_btm=$this->model->all_bottom();
			$contl = $this->model->getPageOptions('allbottom') ?: CONTROLLER_NAME;
			$this->_bottom=$this->fetch(T($contl.'/all_bottom'));
		}
		$js=$this->model->getPageOptions('initJS');
		if(is_string($js)) $js=explode(',', $js);
		$this->initJS=$js;
		$this->display('Common@HyFrame/all');
	}
	
	/**
	 * AJAX请求统一入口
	 * HyAllModel::ajaxs()
	 */
	public function ajax(){
		$log = array('msg'=>'数据请求');
		Hook::listen('hy_log', $log);
		// 对detail请求返回HTML
		if(($query = I('get.q')) === 'detail'){
			return $this->detail();
		}
		$json = array('status'=>false, 'info'=>'', 'data'=>''); // 此$json全程以引用型传递！
		$this->model->ajaxs($query, $json);
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
		$tpl = $query ? 'detailTpl_'.$query : 'detailTpl';
		$tpl = $this->model->getPageOptions($tpl) ?: 'detail'.($type ? '_'.$type : '');
		$this->display($tpl);
	}
}