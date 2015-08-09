<?php

namespace Excel\Model;
use Common\Model\HyAllModel;

/**
 *
 *
 * @author
 */
class ExcelModel extends HyAllModel {
    protected $tableName = 'user';
    protected function initTableName(){
    }

	protected function _initialize(){
	}
	
	/**
	 * @overrides
	 */
	protected function initInfoOptions() {
	}
	/**
	 * @overrides
	 */
	protected function initSqlOptions() {
		return array(
				'decrypt'=>true
		);
	}
	/**
	 * @overrides
	 */
	protected function initListsOptions() {
	}
	/**
	 * @overrides
	 */
	protected function initFieldsOptions() {
	}
    protected function initPageOptions(){
    }

    public function ajax_excel_import(&$json){

        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        import("Org.Util.PHPExcel");
        //要导入的xls文件，位于根目录下的Public文件夹
        $filename='.'.I('path');
        //创建PHPExcel对象，注意，不能少了
        $PHPExcel=new \PHPExcel();
        //如果excel文件后缀名为.xls，导入这个类
        import("Org.Util.PHPExcel.Reader.Excel5");
        //如果excel文件后缀名为.xlsx，导入这下类
//        import("Org.Util.PHPExcel.Reader.Excel2007");
//        $PHPReader=new \PHPExcel_Reader_Excel2007();

        $PHPReader=new \PHPExcel_Reader_Excel5();
        //载入文件
        $PHPExcel=$PHPReader->load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet=$PHPExcel->getSheet(0);
        //获取总列数
        $allColumn=$currentSheet->getHighestColumn();
        //获取总行数
        $allRow=$currentSheet->getHighestRow();
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        for($currentRow=1;$currentRow<=$allRow;$currentRow++){
            //从哪列开始，A表示第一列
            for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
                //数据坐标
                $address=$currentColumn.$currentRow;
                //读取到的数据，保存到数组$arr中
                $arr[$currentRow][$currentColumn]=$currentSheet->getCell($address)->getValue();
            }
        }
        //  dump($this->model);
        //   dump($arr);
       // return $json['data'] = $arr;
        $data_for_user = array();
        $data_for_student = array();
        $error_write = array();
        $error_equal = array();
        foreach($arr as $k=>$v){
            $data_for_user['user_no'] = $v['C'];
            $data_for_user['name'] = $v['D'];
            $data_for_user['roles'] = '21';
            $data_for_user['password'] = 'tHHPF4VYX4V7S7CAryffPdAFtpHlcSZVIL7aUKqVAIEuoQedpaPeSU62_ARBH4fa-6H7qT-vCApYp6CpHSqJn1aUi3XGBsAer2IzMIET7f4TxeJscQxCKd2aNiQe2fyW';
            $data_for_user['sex'] = $v['G'];
            $data_for_user['birth'] = $v['I'];
            $data_for_user['phone'] = val_encrypt($v['E']);
            $data_for_user['qq'] = $v['F'];
            $data_for_user['email'] = $v['H'];
            $data_for_user['country'] = $v['J'];
            $class_name = $v['B'];
            if(!$class_name){
                continue;
            }
            $class_id = M('class')->where(array(
                'name'=>$class_name,
                'status'=>array('eq',1)
            ))->getField('id');
            if(!$class_id) {
                $json['status'] = false;
                $json['info'] = '请先添加班级！';
                return $json;
            }
            $equal_user_id = M('user')->where(array(
                'user_no' => $v['C']
            ))->find();
            if($equal_user_id){
                $error_equal[] =  $v['C'];
                continue;
            }
            $user_id = M('user')->data($data_for_user)->add();
            $data_for_student['user_id'] = $user_id;
            $data_for_student['class_id'] = $class_id;
            $student_id = M('student')->data($data_for_student)->add();
            if($user_id && $class_id && $student_id){
                continue;
            }else{
                $error_write[] = $v['C'];
                break;
            }
        }
        //      dump($error_equal);
        //      dump($error_write);
        $json['status'] = true;
        $json['info'] = '导入成功！';
        $json['data'] = array($error_equal,$error_write);
        return $json;
    }


}
