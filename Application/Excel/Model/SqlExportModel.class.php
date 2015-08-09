<?php

namespace Excel\Model;
use Common\Model\HyAllModel;

/**
 *
 *
 * @author
 */
class SqlExportModel extends HyAllModel {
    protected $tableName = 'class';
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
//		return array(
//				'decrypt'=>true
//		);
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

    public function ajax_sql_export(&$json){

        $grade = I('grade');

        //取出class表的某个年纪的数据
        $class_arr = M('class')->where(array(
            'grade' => $grade
        ))->select();

        //填入备份数据库class表
        foreach($class_arr as $k => $v){
            $eq_class = M('class',C('DB_COPY_PREFIX'),C('DB_COPY_CONFIG'))->where(array(
                'grade' => $v['grade'],
                'name'  => $v['name']
            ))->find();
            if($eq_class) continue;
            else  $result_class = M('class',C('DB_COPY_PREFIX'),C('DB_COPY_CONFIG'))->data($v)->add();
        }

        //删除原class表的记录
        M('class')->where(array(
            'grade' => $grade
        ))->delete();

        //组装班级的id成字符串
        $class_id_arr = array();
        foreach($class_arr as $k=>$v){
            $class_id_arr[] = $v['id'];
        }
        $class_id_str = implode(',',$class_id_arr);


        //取出student表的数据
        $student_arr = M('student')->where(array(
            'class_id' => array(IN,$class_id_str)
        ))->select();

        //填入备份数据库student表
        foreach($student_arr as $k => $v){
            $eq_student = M('student',C('DB_COPY_PREFIX'),C('DB_COPY_CONFIG'))->where(array(
                'user_id' => $v['user_id'],
                'class_id'  => $v['class_id']
            ))->find();
            if($eq_student) continue;
            else  $result_student = M('student',C('DB_COPY_PREFIX'),C('DB_COPY_CONFIG'))->data($v)->add();
        }
        //删除原student表的数据
        M('student')->where(array(
            'class_id' => array(IN,$class_id_str)
        ))->delete();


        //组装user_id成字符串
        $user_id_arr = array();
        foreach($student_arr as $k=>$v){
            $user_id_arr[] = $v['user_id'];
        }
        $user_id_str = implode(',',$user_id_arr);


        //取出user表的数据
        $user_arr = M('user')->where(array(
            'id' => array(IN,$user_id_str)
        ))->select();

        //组装usr_no字符串
        $user_no_arr = array();
        foreach($user_arr as $k=>$v){
            $user_no_arr[] = $v['user_no'];
        }
        $user_no_str = implode(',',$user_no_arr);


        //填入备份数据库user表
        foreach($user_arr as $k => $v){
            $eq_user = M('user',C('DB_COPY_PREFIX'),C('DB_COPY_CONFIG'))->where(array(
                'user_no' => $v['user_no']
            ))->find();
            if($eq_user) continue;
            else  $result_user = M('user',C('DB_COPY_PREFIX'),C('DB_COPY_CONFIG'))->data($v)->add();
        }

        //删除原user表的数据
        M('user')->where(array(
            'id' => array(IN,$user_id_str)
        ))->delete();


        //取出questions表的数据
        $questions_arr = M('questions')->where(array(
            'ask_id' => array(IN,$user_no_str)
        ))->select();

        //填入备份数据库questions表
        foreach($questions_arr as $k => $v){
            $result_questions = M('questions',C('DB_COPY_PREFIX'),C('DB_COPY_CONFIG'))->data($v)->add();
        }
        //删除原questions表的数据
        M('questions')->where(array(
            'ask_id' => array(IN,$user_no_str)
        ))->select();


        //取出score表的数据
        $score_arr = M('score')->where(array(
            'stu_id' => array(IN,$user_id_str)
        ))->select();
        //填入备份数据库score表
        foreach($score_arr as $k => $v){
            $eq_score = M('score',C('DB_COPY_PREFIX'),C('DB_COPY_CONFIG'))->where(array(
                'stu_id' => $v['stu_id']
            ))->find();
            if($eq_score) continue;
            else  $result_score = M('score',C('DB_COPY_PREFIX'),C('DB_COPY_CONFIG'))->data($v)->add();
        }
        //删除score表的数据
        M('score')->where(array(
            'stu_id' => array(IN,$user_id_str)
        ))->delete();
        $sql_export_result = $this->sql_export();

        if($sql_export_result){
            $json['info'] = 'sql文件导出成功! ';
        }else{
            $json['info'] = 'sql文件导出失败! ';
        }
        if($result_class || $result_student || $result_user || $result_questions || $result_score){
            $json['status'] = true;
            $json['info'] .= '备份数据库成功！';
        }else{
            $json['status'] = false;
            $json['info'] .= '备份数据库失败！检查是否重复备份了数据库';
            $json['data'] = array($result_class,$result_student,$result_user,$result_questions,$result_score);
            return $json;
        }
        $json['data'] = array($result_class,$result_student,$result_user,$result_questions,$result_score);
        return $json;
    }

    public function sql_export()
    {
        $table=$this->getTable();
        $struct=$this->bakStruct($table);
        $record=$this->bakRecord($table);
        $sqls=$struct.$record;
        $dir="./data/".date("y-m-d-h-i-s").".sql";
        file_put_contents($dir,$sqls);
        if(file_exists($dir)) {
            return true;
        }else{
            return false;
        }
    }

    protected function getTable()
    {
        $dbName=C('DB_COPY_NAME');
        $result=M()->query('show tables from '.$dbName);
        foreach ($result as $v){
            $tbArray[]=$v['tables_in_'.C('DB_COPY_NAME')];
        }
        return $tbArray;
    }

    protected function bakStruct($array)
    {
        foreach ($array as $v){
            $tbName=$v; //$tbName,C('DB_COPY_PREFIX'),C('DB_COPY_CONFIG')
            $result=M()->query('show columns from '.$tbName);
            $sql.="--\r\n";
            $sql.="-- 数据表结构: `$tbName`\r\n";
            $sql.="--\r\n\r\n";
            $sql.="DROP TABLE IF EXISTS `$tbName`;\r\n";
            $sql.="create table `$tbName` (\r\n";
            $rsCount=count($result);
            // dump($result);die;
            foreach ($result as $k=>$v){
                $field  =       $v['field'];
                $type   =       $v['type'];
                $default=       $v['default'];
                $extra  =       $v['extra'];
                $null   =       $v['null'];
                if(!($default=='')){
                    $default='default '.$default;
                }
                if($null=='NO'){
                    $null='not null';
                }else{
                    $null="null";
                }
                if($v['key']=='PRI'){
                    $key    =       'primary key';
                }else{
                    $key    =       '';
                }
                if($k<($rsCount-1)){
                    $sql.="`$field` $type $null $default $key $extra ,\r\n";
                }else{
                    //最后一条不需要","号
                    $sql.="`$field` $type $null $default $key $extra \r\n";
                }
            }
            $sql.=") ENGINE=MyISAM DEFAULT CHARSET=utf8;\r\n\r\n";
        }
        return str_replace(',)',')',$sql);
    }

    protected function bakRecord($array)
    {

        foreach ($array as $v){

            $tbName=$v;

            $rs=M()->query('select * from '.$tbName);

            if(count($rs)<=0){
                continue;
            }
            $sql.="--\r\n";
            $sql.="-- 数据表中的数据: `$tbName`\r\n";
            $sql.="--\r\n\r\n";

            foreach ($rs as $k=>$v){

                $sql.="INSERT INTO `$tbName` VALUES (";
                foreach ($v as $key=>$value){
                    if($value==''){
                        $value='null';
                    }
                    $type=gettype($value);
                    if($type=='string'){
                        $value="'".addslashes($value)."'";
                    }
                    $sql.="$value," ;
                }
                $sql.=");\r\n\r\n";
            }
        }
        return str_replace(',)',')',$sql);
    }

}
