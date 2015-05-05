<?php
namespace System\Model;
use Think\Hook;
use Common\Model\HyAllModel;

class HyChatModel extends HyAllModel{

	/**
	 * @overrides
	 */
	protected function initTableName(){
		return 'frame_chat';
	}
	
	/**
	 * @overrides
	 */
	protected function initSqlOptions(){
		return array(
				'associate'=>array('user|user_id|id|id user_id,name user_id_text,avatar_file|`college_id`='.ss_clgid()),
				'field'=>'id,content,create_time',
		);
	}
	
	protected function initFieldsOptions(){}
	protected function initInfoOptions(){}
	protected function initPageOptions(){}

	protected function ajax_list(&$json){
		if(false===($json['data']=$this->lists())) return false;
		$json['status']=true;
	}
	
	protected function lists($offset){
		$where['status']=1;
		if($offset) $where=array('id'=>array('gt',$offset));
		else $limit=10;
		$data=$this->order('id desc')->limit($limit)->where($where)->select(array('hy'=>true));
		if(!is_array($data) || !count($data)) return false;
		$max=$data[0]['id'];
		$data=array_reverse($data);
		$userId=ss_uid();
		foreach ($data as $k=>&$v){
			$v['user_id_text']=($v['user_id_text']);
			$v['create_time']=to_time($v['create_time'],5);
			if($userId==$v['user_id']) $v['avatar_file']=session('avatarFile');
			else $v['avatar_file']=avatar_file($v['avatar_file']);
			unset($v['id']);
		}
		return array('list'=>$data,'offset'=>$max);
	}
	
	public function ajax_refresh(&$json){
		$offset=I('offset');
		if(!$offset) return;
		$json['data']=$this->lists($offset);
		$json['status']=!!$json['data'];
	}
	
	public function ajax_add(&$json){
		$offset=I('offset');
		$data['content']=I('content');
		Hook::listen('hy_filter',$data['content']);
		$data['user_id']=ss_uid();
		$data['create_time']=time();
		$data['anonymous']=0;
		if(false===$this->add($data)) return $json['info']=$this->getError();
		$json['status']=true;
		$json['data']=$this->lists($offset);
	}
}