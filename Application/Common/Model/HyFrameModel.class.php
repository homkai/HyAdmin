<?php
namespace Common\Model;
use Think\Model;
use Think\Exception;
use Think\Hook;

/**
 * HyFrame数据管理框架-数据操作扩展模型
 * @author Homkai
 *
 */
abstract class HyFrameModel extends Model {

	// 默认连表当前表别名
	const HYP = 'hy';
	// 框架SQL基础
	protected $sqlOptions = array(
			/**
			 * 连表定义 支持多表
			 * 'associate'=>array('school|school_id|id|name school_name')
			 * 表示 该表中的school_id字段与school表中的id关联
			 * 取出school表中的name 映射成 school_name
			 */
			'associate'	=>	array(),
			// 查询条件，支持连表
			'where'		=>	array(),
			// 查询输出的字段
			'field'		=>	array(),
			// 对所有字段尝试解密
			'decrypt'	=>	false
	);
	protected $insertFields;
	protected $updateFields;
	// select、find查询后置方法集
	protected $afterRead = array();
	
	/**
	 * 重写ThinkPHP Model构造方法
	 */
	public function __construct($name='', $tablePrefix='', $connection='') {
        // 模型初始化
        $this->_initialize();
		if(!empty($name)) {
			if(strpos($name,'.')) {
				list($this->dbName, $this->name) = explode('.', $name);
			} else {
				$this->name   =  $name;
			}
		} elseif(empty($this->name)){
			$this->name =   $this->getModelName();
		}
		if(is_null($tablePrefix)) {
			$this->tablePrefix = '';
		} elseif('' != $tablePrefix) {
			$this->tablePrefix = $tablePrefix;
		} elseif(!isset($this->tablePrefix)){
			$this->tablePrefix = C('DB_PREFIX');
		}
		$this->db(0, empty($this->connection) ? $connection : $this->connection, true);
		// 初始化后置方法
		$this->_after_initialize();
		$this->initSqlWhere();
	}
	/**
	 * 初始化后置方法
	 */
	protected function _after_initialize(){}
	/**
	 * select查询后置方法
	 */
	protected function _after_select(&$result, $options){
		if(!$this->afterRead) return;
		foreach($result as &$record){
			foreach ($this->afterRead as $func){
				$func($record, $options);
			}
		}
	}
	/**
	 * find查询后置方法
	 */
	protected function _after_find(&$result, $options){
		if(!$this->afterRead) return;
		foreach ($this->afterRead as $func){
			$func($result, $options);
		}
	}
	/**
	 * SQL基础配置
	 * @param kvArr $options
	 */
	protected function setSqlOptions($options=array(), $value='', $replace=false){
		if(is_array($options)) {
			$this->sqlOptions = $replace ? $options : array_merge($this->sqlOptions, $options);
		}elseif(is_string($options)){
			$this->sqlOptions[$options] = $value;
		}
	}
	/**
	 * 验证字段
	 * @param smpArr|string $fields
	 */
	public function setUpdateFields($fields){
		$this->updateFields = $fields;
	}
	/**
	 * 验证字段
	 * @param smpArr|string $fields
	 */
	public function getUpdateFields(){
		return $this->updateFields;
	}
	/**
	 * 验证字段
	 * @param smpArr|string $fields
	 */
	public function setInsertFields($fields){
		$this->insertFields = $fields;
	}
	/**
	 * 验证字段
	 * @param smpArr|string $fields
	 */
	public function getInsertFields(){
		return $this->insertFields;
	}
	/**
	 * 重写select方法 以支持associate
	 * @access public
	 * @param array $options 表达式参数
	 * @return mixed
	 */
	public function select($options = array()) {
		if(is_string($options)) $options=array($options=>true);
		if(!$options || (!$options['hy'])) return parent::select($options);
		if(!$options['noReflect']) $associate=(is_array($options) && $options['associate'])?$options['associate']:$this->sqlOptions['associate'];
		$count=$options['count'];
		// 收集table
		$table=$this->options['table'];
		if($associate && !$table[$tmp=$this->getTableName()])
			$table=array($tmp=>self::HYP);
		// 收集limit 不以listsOptions['limit']为默认
		$limit=$this->options['limit']?:null;
		// 收集order
		$order=$this->options['order']?:(!$options['noOrder'] ? $this->listsOptions['order'] : null);
		// 排序字段自动加表别名以避免冲突
		if($order && $associate){
			if(!preg_match('/\(/', $order)){
				$order=array_map(function($v){
					return preg_match('/\./', $v) ? $v : self::HYP.'.'.ltrim($v,' ');
				},explode(',', $order));
			}
		}
		// 收集field
		if(($field=$this->options['field']?:(!$options['noField'] ? $this->sqlOptions['field'] : array())) && $associate) {
			// 防止多表中字段冲突
			if(is_string($field)){
				$field=$this->fieldPre($field, self::HYP);
				if(is_string($field)) $field=array($field);
			}
			if(is_array($field)){
				foreach ($field as $k=>$v)
					if(is_numeric($k)) 
						$field[$k]=$this->fieldPre($field[$k], self::HYP);
			}
		}else{
			$field=$this->getAllFields($field, ($associate ? self::HYP :''));
		}
		// 收集 where - sqlOptions
		if($options['noWhere'])
			$this->sqlOptions['_where']=array('_string'=>'TRUE');
		// 收集where - search
		if(!$options['noSearch'] && method_exists($this, 'searchMap'))
			$this->searchMap();
		// 收集where - TP
		$this->addMap($this->options['where']['_string']);
		unset($this->options['where']['_string']);
		if($this->options['where'])
			foreach ($this->options['where'] as $k=>$v)
				$this->addMap(array($k=>$v));
		foreach ($this->sqlOptions['_where'] as $k=>$v){
			if('_string'==$k) continue;
			unset($this->sqlOptions['_where'][$k]);
			$this->addMap(array($this->fieldPre($k, ($associate ? self::HYP :''))=>$v));
		}
		// 连表处理
		if(is_array($associate) && $associate){
			// 当前表默认别名 HYP常量
			$join=$this->options['join']?:array();
			foreach ( $associate as $v ) {
				$rflcConf = explode ( '|', $v );
				preg_match('/([\w\.\-\(\)]+)\s*(AS)*\s*([\w\-]*)/i', $rflcConf[0],$r);
				$rflcConf[0]=$r[3]?:$r[1];
				if($rflcConf[4]) {
					$pre=$rflcConf[0].'.';
					$rflcConf[4]=preg_replace_callback('/`([\w\-]+)`/', function($matches) use($pre){
						if($matches[1]) return $pre.$matches[1];
					}, $rflcConf[4]);
				}
				$rflcConf[5]=$rflcConf[5]?strtoupper($rflcConf[5]):'INNER';
				$join[]=$rflcConf[5].' JOIN `'.DTP.$r[1].'` AS `'.$rflcConf[0].'` ON '.(strpos($rflcConf[1],'.') ? '' : 'hy.').$rflcConf[1].' = '.$rflcConf[0].'.'.$rflcConf[2].($rflcConf[4] ? ' AND '.$rflcConf[4] : '');
				if (!$rflcConf [3]) continue;
				// 使用 \, 处理定义字段时可能使用函数而带来的逗号
				$rflcConf [3]=str_replace('\,', '#HY#', $rflcConf [3],$c);
				foreach ( explode ( ',', $rflcConf [3] ) as $show ) {
					if($c)$show=str_replace('#HY#', ',', $show);
					if(preg_match('/([\w\.\-\(\)]+)\s*(AS)*\s*([\w\-]*)/i', $show,$r)){
						$r[1]=strpos($r[1], '.')?$r[1]:$rflcConf[0].'.'.$r[1];
						if($r[3]) $field[$r[1]]=$r[3];
						else $field[]=$r[1];
					}
				}
			}
		}
		$_options=array('table'=>$table, 'where'=>$this->sqlOptions['_where'], 'field'=>$field, 'order'=>$order, 'join'=>$join, 'limit'=>$limit);
// 		dump($_options);
		$this->initSqlWhere();
		if($options['decrypt']) $this->sqlOptions['decrypt'] = true;
		if($this->sqlOptions['decrypt']){
			$this->afterRead[] = function(&$result){
				array_walk($result, 'val_decrypt_quote');
			};
		}
		if($options['scope']) $this->_scope[$options['scope']] = $_options;
		if($count) {
			$_options['field'] = '1';
			return count(parent::select($_options));
		}
		return parent::select($_options);
	}
	/**
	 * cout统计 默认整合自定义sql配置
	 * @param string $field | hy
	 */
	public function count($field = ''){
		if(!$field) return parent::count('1');
		if(is_string($field) && 'hy' != $field) return parent::count($field);
		$cmd = array_merge(is_array($field) ? $field : array(), array('count'=>true, 'hy'=>true));
		return $this->select($cmd);
	}
	/**
	 * 重写find方法 以支持associate
	 */
	public function find($options){
		if('hy'==strtolower($options) || $options['hy'] || ( is_array($options) && ( $options['associate'] || $options['count']))){
			$this->options['limit'] = 1;
			$data=$this->select($options);
			return $data[0];
		}else{
			return parent::find($options);
		}
	}
	/**
	 * 连表查询 - 连贯操作
	 * @param array $associate
	 */
	public function associate($associate = array()){
		if(is_array($associate)){
// 			$this->sqlOptions['_where'] = array('_string'=>'TRUE');
			$this->sqlOptions['associate'] = $associate;
		}
		return $this;
	}
	/**
	 * 字符串fields处理
	 * @param string $str
	 * @param string $pre
	 * @return string
	 */
	private function fieldPre($field, $pre=''){
		if($pre) $pre .= '.';
		if(!preg_match('/(\(|\,|\.)/', $field)){
			$field = $pre.ltrim($field, ' ');
		}elseif(!preg_match('/\(/', $field)){
			$field=implode(',', array_map(function($v) use($pre){
				return preg_match('/\./', $v) ? $v : $pre.ltrim($v,' ');
			}, explode(',', $field)));
		}else{
			$field = preg_replace_callback('/`(.+)`/', function($matches) use($pre){
				if($matches[1]) return $pre.$matches[1];
			}, $field);
		}
		return $field;
	}
	/**
	 * 增加where条件 支持字符串和键值对
	 * @param string $k
	 * @param array $v
	 */
	protected function addMap($map=''){
		if(!$map) return ;
		// 初始化			
		if(is_string($map)) {
			$this->sqlOptions['_where']['_string'] .= " and ( $map ) ";
			return;
		}
		if(is_array($map)){
			$k = key($map);
			$v = $map[$k];
			if(!isset($this->sqlOptions['_where'][$k]))
				$this->sqlOptions['_where'][$k] = $v;
			else{
				if(is_array($this->sqlOptions['_where'][$k][0])) array_unshift($this->sqlOptions['_where'][$k], $v);
				else $this->sqlOptions['_where'][$k] = array($v, $this->sqlOptions['_where'][$k], 'AND');
			}
		}
	}
	/**
	 * 取得模型查询字段，自定义表别名
	 *
	 * @param smpArr $except
	 * @param string $pre
	 * @return string
	 */
	protected function getAllFields($custom = array(), $pre = '') {
		$fields = array();
		if($custom && is_array($custom)){
			foreach ( $custom as $k => $v ) {
				if(is_numeric($k)){
					$v = strpos($v, '.') ? $v : "$pre.$v";
					$fields[] = $v;
				}else{
					$k = strpos($k, '.') ? $k : "$pre.$k";
					$fields[$k]=$v;
				}
			}
			return $fields;
		}
		// 若未自定义，则返回所有字段
		if(!$this->fields) E('模型初始化失败！请检查是否定义了正确的数据表名！');
		foreach ( $this->fields as $k => $v ) {
			if(!is_numeric($k)) continue;
			if($pre) $fields[] = "$pre.$v";
			else $fields[] = $v;
		}
		return $fields;
	}
	/**
	 * 初始化Sql-where
	 */
	private function initSqlWhere(){
		$this->sqlOptions['where']['_string'] = $this->sqlOptions['where']['_string'] ?: 'TRUE';
		// 拷贝sql-where
		$this->sqlOptions['_where'] = $this->sqlOptions['where'];
	}
	/**
	 * 获得无前缀表名
	 * @return string
	 */
	protected function getTableNameHy(){
		if($this->tableName) return $this->tableName;
		return substr($this->getTableName(), strlen(DTP));
	}
	/**
	 * 设定模型出错信息
	 * @param string $info
	 * @return boolean
	 */
	protected function setError($info = ''){
		$this->error=$info;
		$log=array('msg'=>$this->error, 'style'=>'text-danger');
		Hook::listen('hy_log', $log);
		return false;
	}
}
