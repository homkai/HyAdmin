<?php
namespace System\Model;
use Think\Upload;
use Common\Model\HyFrameModel;
/**
 * 文件模型
 * 负责文件的下载和上传
 */

class HyFileModel extends HyFrameModel{
	
	protected $tableName='frame_file';
	
    /**
     * 文件模型自动完成
     * @var array
     */
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );

    /**
     * 文件模型字段映射
     * @var array
     */
    protected $_map = array(
        'type' => 'mime',
    );

    /**
     * 文件上传
     * @param  array  $files   要上传的文件列表（通常是$_FILES数组）
     * @param  array  $setting 文件上传配置
     * @param  string $driver  上传驱动名称
     * @param  array  $config  上传驱动配置
     * @return array           文件上传成功后的信息
     */
    public function upload($files, $setting, $driver = 'Local', $config = null){
        /* 上传文件 */
    	$setting = C('FILE_UPLOAD');
        $setting['callback'] = array($this, 'isFile');
		$setting['removeTrash'] = array($this, 'removeTrash');
        $Upload = new Upload($setting, $driver, $config);
        $info   = $Upload->upload($files);
// dump($info);
        /* 设置文件保存位置 */
		$this->_auto[] = array('location', 'local' === strtolower($driver) ? 0 : 1, self::MODEL_INSERT);

        if($info){ //文件上传成功，记录文件信息
            foreach ($info as $key => &$value) {
                /* 已经存在文件记录 */
                if(isset($value['id']) && is_numeric($value['id'])){
                    $value['path'] = substr($setting['rootPath'], 1).$value['savepath'].$value['savename']; //在模板里的url路径
                    continue;
                }
                $value['path'] = substr($setting['rootPath'], 1).$value['savepath'].$value['savename']; //在模板里的url路径
                /* 记录文件信息 */
                if($this->create($value) && ($id = $this->add())){
                    $value['id'] = $id;
                } else {
                    //TODO: 文件上传成功，但是记录文件信息失败，需记录日志
                    unset($info[$key]);
                }
            }
            return 1===count($info) ? $value : $info; //文件上传成功
        } else {
            $this->error = $Upload->getError() ?: '文件上传失败！';
            return false;
        }
    }
    /**
     * 根据文件信息获取静态路径
     * @param unknown $file
     * @return string
     */
    public function getStaticPath($file,$server=false){
        $root = C('FILE_UPLOAD.rootPath');
        if($server) return $root.$file['savepath'].$file['savename'];
    	return __ROOT__.ltrim($root,'.').$file['savepath'].$file['savename'];
    }

    /**
     * 下载指定文件
     * @param  number  $root 文件存储根目录
     * @param  integer $id   文件ID
     * @param  string   $args     回调函数参数
     * @return boolean       false-下载失败，否则输出下载文件
     */
    public function download( $id, $static = false, $callback=array()){
        /* 获取下载文件信息 */
    	if(!$id) return false;
        $file = $this->getById($id);
        if(!$file){
            $this->error = '不存在该文件！';
            return false;
        }
        if($static) return $this->getStaticPath($file);
        /* 下载文件 */
        switch ($file['location']) {
            case 0: //下载本地文件
                $file['rootpath'] = C('FILE_UPLOAD.rootPath');
                return $this->downLocalFile($file, $callback);
            default:
                $this->error = '不支持的文件存储类型！';
                return false;

        }
    }

    /**
     * 检测当前上传的文件是否已经存在
     * @param  array   $file 文件上传数组
     * @return boolean       文件信息， false - 不存在该文件
     */
    public function isFile($file){
        if(empty($file['md5'])){
            throw new \Exception('缺少参数:md5');
        }
        /* 查找文件 */
        $map = array('md5' => $file['md5']);
        return $this->field(true)->where($map)->find();
    }

    /**
     * 下载本地文件
     * @param  array    $file     文件信息数组
     * @param  callable $callback 下载回调函数，一般用于增加下载次数
     * @param  string   $args     回调函数参数
     * @return boolean            下载失败返回false
     */
    private function downLocalFile($file, $callback){
        if(is_file($file['rootpath'].$file['savepath'].$file['savename'])){
            /* 调用回调函数新增下载数 */
            is_callable($callback[0]) && call_user_func_array($callback[0],$callback[1]);

            /* 执行下载 */ //TODO: 大文件断点续传
            header("Content-Description: File Transfer");
            header('Content-type: ' . $file['type']);
            header('Content-Length:' . $file['size']);
            if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
                header('Content-Disposition: attachment; filename="' . rawurlencode($file['name']) . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . $file['name'] . '"');
            }
            readfile($file['rootpath'].$file['savepath'].$file['savename']);
            exit;
        } else {
            $this->error = '文件已被删除！';
            return false;
        }
    }

	/**
	 * 清除数据库存在但本地不存在的数据
	 * @param $data
	 */
	public function removeTrash($data){
		$this->where(array('id'=>$data['id']))->delete();
	}

}
