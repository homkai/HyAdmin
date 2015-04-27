<?php
namespace User\Controller;

/**
 * 用户相关控制器
 * @author Homkai
 *
 */
class UserController extends HyAllController {
	
     public function profile(){
    	$this->assign('user',$this->model->profile())->display();
    }
    
    public function search(){
    	$this->clsOpts=HomkaiModel::getClassOptg();
    	$this->assign('search',$this->model->search($pk))->display();
    }
}