<?php
namespace System\Controller;
use Think\Controller;

// 数据表前缀
defined('DTP') or define('DTP', C('DB_PREFIX'));
// 当前时间戳
defined('TIME') or define('TIME', time());

class TestController extends Controller{
	
	public function test(){
		$model = D('User');
		$data = $model->select();
	}
	
}