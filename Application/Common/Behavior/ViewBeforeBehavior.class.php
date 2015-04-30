<?php 
namespace Common\Behavior;
use Think\Behavior;
/**
 * 视图输出前
 */
class ViewBeforeBehavior extends Behavior{
	
	public function run(&$the) {
		$the->_assets = $this->loadAssets();
		if(ss_uid()){
			$the->hyAlerts = $this->getUnreadList();
		}
	}
	
	/**
	 * 输出消息提醒数据
	 */
	protected function getUnreadList(){
		return D('System/HyAlert')->getUnreadList();
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
			return $assets;
		}
	}
}