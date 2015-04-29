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
			 * 'associate'=>array('user|user_id|id|name user_name,sex|`status`=1|LEFT')
			 * 0-2=>表示 该表中的user_id字段与user表中的id关联
			 * 3=>取出user表中的name（映射成 user_name）和sex字段（可选）
			 * 4=>限制user表中的status字段为1（可选）
			 * 5=>采用LEFT JOIN 默认INNER JOIN（可选）
			 * 详见associate方法
			 */
			'associate'	=>	array(),
			// 查询条件，支持连表：
			'where'		=>	array(),
			// 查询输出的字段：
			'field'		=>	array(),
			// 对所有字段尝试解密：
			'decrypt'	=>	false
	);
	protected $insertFields;
	protected $updateFields;
	// select、find查询后置方法集
	protected $afterRead = array();
	// associate数据
	private $_associate = array();
	
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
	 * 判断是否执行框架的模型方法
	 * @param unknown $options
	 * @return boolean
	 */
	private function isCallHyFunc($options){
		$hy = $options['hy'];
		$associate = $this->_associate || ($this->sqlOptions['associate'] && $hy) || $options['associate'];
		return $associate || $hy ? array($hy, $associate) : false;
	}
	/**
	 * 重写select方法 以支持associate和sqlOptions
	 * @access public
	 * @param array $options 表达式参数
	 * @return mixed
	 */
	public function select($options = array()) {
		if(!$arr = $this->isCallHyFunc($options)) return parent::select($options);
		list($hy, $associate) = $arr;
		if($associate) $associate = array_merge($hy ? $this->sqlOptions['associate'] : array(), $this->_associate);
		// 收集table
		if($associate) $this->table(array($this->getTableName()=>self::HYP));
		// 收集limit
		if($hy && !$this->options['limit']) $this->limit($this->pageOptions['limit']);
		// 收集order
		if($order = $this->options['order'] ?: ($hy ? $this->pageOptions['order'] : null)){
			// 排序字段自动加表别名以避免冲突
			if($associate && $hy && $order && is_string($order) && false===strpos($order, '(')){
				$order = implode(',', array_map(function($v){
					return false!==strpos($v, '.') ? $v : self::HYP.'.'.ltrim($v,' ');
				}, explode(',', $order)));
			}
			$this->options['order'] = $order;
		}
		// 收集field
		if($associate && ($field = $this->options['field'] ?: ($hy ? $this->sqlOptions['field'] : array()))) {
			// 防止多表中字段冲突
			if(is_string($field)){
				$field = $this->fieldPre($field, self::HYP);
				if(is_string($field)) $field = array($field);
			}
			if(is_array($field)){
				foreach ($field as $k=>$v)
					if(is_numeric($k)) $field[$k] = $this->fieldPre($field[$k], self::HYP);
			}
		}else{
			$field = $this->getAllFields($field, ($associate ? self::HYP : ''));
		}
		// 收集 where - sqlOptions
		if($hy){
			// 拷贝副本，不影响下次使用sqlOptions['where']
			$this->sqlOptions['_where'] = $this->sqlOptions['where'];
			// 收集where - search
			if($hy && method_exists($this, 'searchMap')) $this->searchMap();
			if($this->options['where']['_string']){
				$this->addMap($this->options['where']['_string']);
				unset($this->options['where']['_string']);
			}
			// 整合到TP
			$this->where($this->sqlOptions['_where']);
		}
		// where条件中的字段避免冲突
		if($associate && $this->options['where']){
			$arr = array();
			foreach ($this->options['where'] as $k=>$v){
				if(0===strpos($k, '_')) continue;
				$arr[$this->fieldPre($k, self::HYP)] = $v;
				unset($this->options['where'][$k]);				
			}
			if($arr) $this->where($arr);
		}
		// 连表处理
		if(is_array($associate) && $associate){
			// 当前表默认别名 HYP常量
			$join = array();
			foreach ( $associate as $v ) {
				$asc = explode ( '|', $v );// 0=>关联表名（支持别名），1=>当前表外键，2=>关联表主键，3=>要从关联表取出的字段（多个以“，”分隔），4=>限制条件，5=>join类型（默认inner）
				preg_match('/([\w\.\-\(\)]+)\s*(AS)*\s*([\w\-]*)/i', $asc[0], $m);
				$asc[0] = $m[3] ?: $m[1];
				if($asc[4]) {
					$pre = $asc[0].'.';
					$asc[4] = preg_replace_callback('/`([\w\-]+)`/', function($m1) use($pre){
						if($m1[1]) return $pre.$m1[1];
					}, $asc[4]);
				}
				$asc[5] = $asc[5] ? strtoupper($asc[5]) : 'INNER';
				$join[] = $asc[5].' JOIN `'.DTP.$m[1].'` AS `'.$asc[0].'` ON '.(strpos($asc[1],'.') ? '' : self::HYP.'.').$asc[1].' = '.$asc[0].'.'.$asc[2].($asc[4] ? ' AND '.$asc[4] : '');
				if (!$asc[3]) continue;
				// 使用 ,, 处理定义字段时可能使用函数而带来的逗号
				$asc[3] = str_replace(',,', '#HY#', $asc[3],$c);
				foreach(explode (',', $asc[3]) as $show){
					if($c) $show = str_replace('#HY#', ',', $show);
					if(preg_match('/([\w\.\-\(\)]+)\s*(AS)*\s*([\w\-]*)/i', $show, $m)){
						$m[1] = strpos($m[1], '.') ? $m[1] : $asc[0].'.'.$m[1];
						if($m[3]) $field[$m[1]] = $m[3];
						else $field[] = $m[1];
					}
				}
			}
			$this->join($join);
		}
		$this->field($field);
		// 清理现场
		$this->sqlOptions['_where'] = null;
		$this->_associate = array();
		// 全字段尝试解密
		if($options['decrypt']) $this->sqlOptions['decrypt'] = true;
		if($this->sqlOptions['decrypt']) {
			$this->afterRead[] = function(&$result){
				array_walk($result, 'val_decrypt_quote');
			};
		}
		if($options['scope']) $this->_scope[$options['scope']] = $this->options;
		if($options['count']) {
			$this->options['order'] = null;
			$this->options['limit'] = null;
			$this->options['field'] = '1';
			return count(parent::select());
		}
		if($options['find']) {
			$this->options['limit'] = 1;
		}
		return parent::select();
	}
	/**
	 * 重写count方法 以支持associate和sqlOptions
	 * @param string $field | hy
	 */
	public function count($options){
		if(!$this->isCallHyFunc($options)) return parent::count($options);
		return $this->select(array_merge(is_array($options) ? $options : array(), array('count'=>true)));
	}
	/**
	 * 重写find方法 以支持associate和sqlOptions
	 */
	public function find($options){
		if(!$this->isCallHyFunc($options)) return parent::find($options);		
		$data = $this->select(array_merge(is_array($options) ? $options : array(), array('find'=>true)));
		return $data[0];
	}
	/**
	 * 连表查询连贯操作 支持多次调用
	 * @param array $associate 
	 * @example array(user|user_id|id|name,sex|`status`=1|LEFT)
	 * @tutorial 0=>关联表名（支持别名），1=>当前表外键，2=>关联表主键，3=>要从关联表取出的字段（多个以“，”分隔），4=>限制条件（字段加``），5=>join类型（默认inner）
	 */
	public function associate($associate = array()){
		if(is_array($associate)){
			$this->_associate = array_merge($this->_associate, $associate);
		}
		return $this;
	}
	/**
	 * fields加前缀处理
	 * @param string $str
	 * @param string $pre
	 * @return string
	 */
	private function fieldPre($field, $pre = ''){
		if($pre) $pre .= '.';
		if(!preg_match('/(\(|\,|\.)/', $field)){
			$field = $pre.ltrim($field, ' ');
		}elseif(!preg_match('/\(/', $field)){
			$field = implode(',', array_map(function($v) use($pre){
				return false!==strpos($v, '.') ? $v : $pre.ltrim($v, ' ');
			}, explode(',', $field)));
		}else{
			$field = preg_replace_callback('/`(.+)`/', function($matches) use($pre){
				if($matches[1]) return $pre.$matches[1];
			}, $field);
		}
		return is_array($field) ? implode(',', $field) : $field;
	}
	/**
	 * 增加where条件 支持字符串和键值对
	 * 当两次定义了某个字段的查询条件时，默认AND方式组合
	 * @param string $k
	 * @param array $v
	 */
	protected function addMap($map=''){
		if(!$map) return;
		if(is_string($map)) {
			if(!$this->sqlOptions['_where']['_string']) $this->sqlOptions['_where']['_string'] = $map;
			else $this->sqlOptions['_where']['_string'] .= " AND ( $map ) ";
			return;
		}
		if(is_array($map)){
			$k = key($map);
			$v = $map[$k];
			if(!isset($this->sqlOptions['_where'][$k])){
				$this->sqlOptions['_where'][$k] = $v;
			}else{
				if(is_array($this->sqlOptions['_where'][$k][0])){
					array_unshift($this->sqlOptions['_where'][$k], $v);
				}else{
					$this->sqlOptions['_where'][$k] = array($v, $this->sqlOptions['_where'][$k], 'AND');
				}
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
