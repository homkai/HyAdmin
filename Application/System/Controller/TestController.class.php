<?php
namespace System\Controller;
use Think\Controller;

// 数据表前缀
defined('DTP') or define('DTP', C('DB_PREFIX'));
// 当前时间戳
defined('TIME') or define('TIME', time());

class TestController extends Controller{
	
	public function test(){
		dump('Start');
		$model = new \System\Model\UserModel();
		$arr = $model->associate(array('teacher|id|user_id|job'))->order('id asc')->limit(1)->field('id')->select();
		dump($arr);
	}
	
}