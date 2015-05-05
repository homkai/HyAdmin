<?php
namespace System\Controller;
use Common\Controller\HyFrameController;
use System\Model\HyFileModel;

/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class HyFileController extends HyFrameController {
	
	protected function _initialize(){
		$this->model = new HyFileModel();
	}

    /**
     * 文件上传入口
     */
    public function upload(){
    	// TODO 目前只支持单一文件异步上传
		$return  = array('status' => 0, 'info' => '', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$info = $this->model->upload($_FILES);
        /* 记录附件信息 */
        if($info){
        	$return['status'] = 1;
            $return['data'] = array('path'=>$info['path'], 'key'=>token_builder($info['id']));
            $return['info'] = $info['name'].' 上传成功！';
        } else {
            $return['info']   = $this->model->getError();
        }
        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }

    /**
     * 文件下载
     * 文件下载链接生成函数file_down_url($id)
     * @param string $id
     */
    public function download($id = null){
        if(empty($id) || !is_numeric($id=file_id_decrypt($id))){
            $this->error('参数错误！');
        }
        $rst=$this->model->download($id);
        if(!$rst) $this->error($this->model->getError());
    }

}
