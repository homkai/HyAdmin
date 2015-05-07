<?php
namespace Common\Model;
use Think\Hook;

/**
 * HyFrame数据管理框架 - 管理页模型
 * 
 * 管理页列表显示、检索、删除、新增、编辑、详情弹窗等
 * 
 * @author Homkai
 */
abstract class HyAllModel  extends HyFrameModel{
	
	/**
	 * 数据表名
	 * @var string
	 */
	protected $tableName = '';
	
	/**
	 * 模型基础信息
	 * 
	 * 'title'		=>	'' // 标题
	 * 
	 * 'subtitle'	=>	'' // 副标题
	 * 
	 * @var array
	 */
	protected $infoOptions = array(
			'title'		=>	'',
			'subtitle'	=>	''
	);
	
	/**
	 * 字段配置信息
	 *  
	 *  说明：
	 *  
	 *  1、title可继承自上一级！（list.title = list.title ?: ROOT.title）
	 *  
	 *  2、如果无title则当前组无效（list = !list.title ? [] : list）
	 *  
	 *  3、表现类型：text|select|date|datetime|dateRange|textarea|file|html...
	 *  
	 *  4、callback：array类型，第一个参数为callback名，默认传入回调方法的第一个参数为自身，可通过{#}指定自身的参数位置，可通过{:field}获取其他字段的值
	 *  array('tplReplace','{:class_id_text}','<a href="#">{0}</a>',{#})
	 *  通过$GLOBALS['callbackKey']可直接取$key，支持递归调用
	 *  
	 *  5、field、order、associate等出现字段的地方，如果为当前表的字段，会自动判断是否连表，对于连表的情况会自动加上当前表别名以避免冲突。
	 *  如果情况复杂，如使用了函数，则当前表的字段应加上``，如果非当前表的字段，必须加上表名
	 *  
	 *  字段名 => 配置数组：
	 *   
	 * 	fieldName(ROOT) => [
	 * 		 字段显示名称：
	 * 		'title'	=>	''
	 * 		 数据表：
	 * 		'table'	=>	''
	 * 		 列表显示配置：
	 * 		'list'	=>	[
	 * 			 列表字段显示名称：
	 * 			'title'		=>	''
	 * 			 隐藏字段：
	 * 			'hidden'	=>	false
	 * 			 排序（为false则关闭排序，字符串为自定义排序，如排序字典）：
	 * 			'order'		=>	true
	 * 			 字段值回调方法：
	 * 			'callback'	=>	[]
	 * 			 宽度：
	 * 			'width'		=>	''
	 * 			 样式：
	 * 			'style'		=>	''
	 * 			 检索配置：
	 * 			'search'	=>	[
	 * 				 检索字段显示名称：
	 * 				'title'	=>	''
	 * 				 检索表现类型（text|select|date...）：
	 * 				'type'	=>	''
	 * 				 SQL匹配方式（eq|like|lt|gt...）				 
	 * 				'query'	=>	''
	 * 				 自定义检索SQL字符串：
	 * 				'sql'	=>	''
	 * 				 自定义回调方法生成SQL：
	 * 				'callback'=>[]
	 * 			]
	 * 		]
	 * 		 表单显示配置：
	 * 		'form' 	=>	[
	 * 			 表单字段显示名称：
	 * 			'title'		=>	''
	 * 			 字段表现类型（text|select|date...）：
	 * 			'type'		=>	''
	 * 			 启用新增表单：
	 * 			'add'		=>	boolean
	 * 			 启用编辑表单：
	 * 			'edit'		=>	boolean
	 * 			 表单字段值回调方法：
	 * 			'callback'	=>	[]
	 * 			 表单验证规则：
	 * 			'validate'	=>	[] // jQuery Validation Rules
	 * 			 服务器端验证规则：
	 * 			'server'	=>	[]
	 * 			 表单额外属性：
	 * 			'attr'		=>	''
	 * 			 表单样式：
	 * 			'style'		=>	''
	 * 			 如果type为daterange类型，则需配置：
	 * 			'daterange'	=>	[
	 * 				 起始时间：
	 * 				'from'	=>	''
	 * 				 截止时间：
	 * 				'to'	=>	''
	 * 			]
	 * 			 如果type为file类型，则可选配置：
	 * 			'file'		=>	[
	 * 				可接受文件扩展名：
	 * 				'ext'	=>	''
	 * 			]
	 * 			 如果type为select培训，则可选配置：
	 * 			'select'	=>	[
	 * 				 是否支持多选：
	 * 				'multiple'	=>	false
	 * 				 是否支持新增其他选项：
	 * 				'addon'		=>	false
	 * 				 提示信息：
	 * 				'first'		=>	''
	 * 				 是否开启分组选择：
	 * 				'optgroup'	=>	false
	 * 			]
	 * 			 字段填充：
 	 * 			'fill'		=>	[
 	 *				 填充方式：
	 * 				'both'|'edit'|'add'	=>	[] // callback
	 * 			]
	 * 		]
	 * 	]
	 * 					
	 * @var array
	 */
	protected $fieldsOptions = array ();
	
	/**
	 * 管理页配置
	 *
	 *  配置说明：
	 *
	 * 	options => [
	 *		 表格类型（暂时仅支持AJAX方式） ：
	 *		'type'		=>	'ajax'
	 *		 响应式表格开关 ：
	 *		'dtResponsive'=>	true
	 *		 物理删除 ：delete | 逻辑删除：status|9 （status为逻辑删除的字段，9为临界值）：
	 *		'deleteType'=>	'delete',
	 *		 分页限制（同ThinkPHP的limit） ：
	 *		'limit'		=>	10,
	 *		 字段排序开关 ：
	 *		'sort'		=>	true,
	 *		 默认排序字段（同ThinkPHP的order） ：
	 *		'order'		=>	$this->getPk().' desc',
	 *		 打印开关：
	 *		'print'		=>	true,
	 *		 导出 - xls(csv)、pdf：
	 *		'export'		=>	'xls',
	 *		 序号开关 ：
	 *		'number'		=>	false,
	 *		 显示全部 ：
	 *		'all'		=>	true,
	 *		 复选框 ：
	 *		'checkbox'	=>	true,
	 *		 操作成功刷新页面 ：
	 *		'okRefresh'	=>	false,
	 *		 操作组 ：
	 *		'actions'	=>	[
	 *			'edit'		=>	[
	 *				'title'		=>	'编辑',
	 *				'max'		=>	1 		// 同时操作的记录数
	 *			],
	 *			'delete'		=>	[
	 *				'title'		=>	'删除',
	 *				'confirm'	=>	true 	// 是否需要确认
	 *			]
	 *		],
	 *		 按钮组 ：
	 *		'buttons'	=>	[
	 * 			'add'		=>	[
	 * 				'title'		=>	'新增',
	 * 				'icon'		=>	'fa-plus'
	 * 			]
	 *		],
	 *		 表单弹窗尺寸：
	 *		'formSize'	=>	'default',
	 *		 多表数据写入 ：
	 *		'tablesWrite'=>	false,
	 *		 主键检查 ：
	 *		'checkPk'	=>	[
	 *			'edit'		=>	false,
	 *			'delete'	=>	false,
	 *			'detail'	=>	false
	 *		],
	 *		 详情模板 ：
	 *		'detailTpl'	=>	'Common@HyFrame/detail',
	 *		 编辑数据时重新发送pageOptions ：
	 *		'editFields'	=>	false
	 * 	]
	 *
	 * @var array
	 */
	protected $pageOptions	= array();
	
	/**
	 * 自定义面包屑
	 * 
	 * 不同的操作对应不同的面包屑：ACTION_NAME=>''
	 * @var array
	 */
	protected $breadcrumb = array();
	
	/**
	 * 初始化方法
	 * 
	 * 定义管理页默认配置
	 */
	protected function _initialize(){
		parent::_initialize();
		$this->tableName = $this->initTableName();
		$this->pageOptions = array(
			// 表格类型
			'type'		=>	'ajax',
			// 响应式表格
			'dtResponsive'=>true,
			// 删除 | 禁用：forbid <=> forbid|status|9
			'deleteType'=>	'delete',
			// 分页限制
			'limit'		=>	10,
			// 开启字段排序
			'sort'		=>	true,
			// 默认排序字段
			'order'		=>	$this->getPk().' desc',
			// 打印
			'print'		=>	true,
			// 导出 - xls(csv)、pdf
			'export'	=>	'xls',
			// 序号
			'number'	=>	false,
			// 显示全部
			'all'		=>	true,
			// 复选框
			'checkbox'	=>	true,
			// 操作成功刷新页面
			'okRefresh'	=>	false,
			// 操作组
			'actions'	=>	array(
					'edit'	=>	array(
							'title'		=>	'编辑',
							'max'		=>	1
					),
					'delete'=>	array(
							'title'		=>	'删除',
							// 是否需要确认
							'confirm'	=>	true
					)
			),
			// 按钮组
			'buttons'	=>	array(
					'add'	=>	array(
							'title'	=>	'新增',
							'icon'	=>	'fa-plus'
					)
			),
			'formSize'		=>	'default',
			// 多表数据写入
			'tablesWrite'	=>	false,
			// 主键检查
			'checkPk'		=>	array(
					'edit'		=>	false,
					'delete'	=>	false,
					'detail'	=>	false
			),
			// 详情模板
			'detailTpl'		=>	'Common@HyFrame/detail',
			// 编辑数据时重新发送pageOptions
			'editFields'	=>	false
		);
	}
	/**
	 * 初始化模型
	 */
	protected function _after_initialize(){
		parent::_after_initialize();
		$this->setInfoOptions($this->initInfoOptions());
		$this->setFieldsOptions($this->initFieldsOptions());
		$this->setPageOptions($this->initPageOptions());
		$this->setSqlOptions($this->initSqlOptions());
	}
	/**
	 * 指定数据表名
	 * @return string
	 */
	protected abstract function initTableName();
	
	/**
	 * 指定模型基础配置
	 * 
	 * 参考self::$infoOptions
	 * @return array
	 */
	protected abstract function initInfoOptions();
	
	/**
	 * 指定管理页配置
	 * 
	 * 参考self::$pageOptions
	 * @return array
	 */
	protected abstract function initPageOptions();
	
	/**
	 * 指定字段显示配置
	 * 
	 * 参考self::$fieldsOptions
	 * @return array
	 */
	protected abstract function initFieldsOptions();
	
	/**
	 * 指定SQL基础配置
	 * 
	 * 参考HyFrameModel::$sqlOptions
	 * @return array
	 */
	protected abstract function initSqlOptions();
	
	/**
	 * 管理页面配置
	 * @param array|string $options 整个配置项或某项的名称
	 * @param string|array $value [配置的值]
	 */
	protected function setPageOptions($options=array(), $value=''){
		if(is_array($options)) {
			$this->pageOptions = (true===$value) ? $options : array_merge($this->pageOptions,$options);
		}elseif(is_string($options)){
			$this->pageOptions[$options]=$value;
		}
	}
	/**
	 * 获取页面配置
	 * @param string $key 配置项的名称
	 * @return array|string
	 */
	public function getPageOptions($key){
		if($key) return $this->pageOptions[$key];
		return $this->pageOptions;
	}
	/**
	 * 字段信息配置
	 * @param array|string $options 整个配置项或某项的名称
	 * @param string|array $value [配置的值]
	 */
	protected function setFieldsOptions($options=array(), $value=''){
		if(is_array($options)) {
			$this->fieldsOptions = (true===$value) ? $options : array_merge($this->fieldsOptions,$options);
		}elseif(is_string($options)){
			$r=explode(',', $options);
			if($r[3]) $this->fieldsOptions[$r[1]][$r[2]][$r[3]][$r[4]] = $value;
			elseif($r[2]) $this->fieldsOptions[$r[1]][$r[2]][$r[3]] = $value;
			elseif($r[1]) $this->fieldsOptions[$r[1]][$r[2]] = $value;
			elseif($r[0]) $this->fieldsOptions[$r[1]] = $value;
			return false;
		}
	}
	/**
	 * 模型基础信息配置
	 * @param array|string $options 整个配置项或某项的名称
	 * @param string|array $value [配置的值]
	 */
	protected function setInfoOptions($options=array(), $value=''){
		if(is_array($options)) {
			$this->infoOptions = (true===$value) ? $options : array_merge($this->infoOptions,$options);
		}elseif(is_string($options)){
			$this->infoOptions[$options]=$value;
		}
	}
	/**
	 * 模型SQL基础配置
	 * @param array|string $options 整个配置项或某项的名称
	 * @param string|array $value [配置的值]
	 */
	protected function setSqlOptions($options=array(), $value='', $replace=false){
		if(is_array($options)) {
			$this->sqlOptions = $replace ? $options : array_merge($this->sqlOptions, $options);
		}elseif(is_string($options)){
			$this->sqlOptions[$options] = $value;
		}
	}
	/**
	 * 获取模型信息
	 * @return string
	 */
	public function getInfo($key = ''){
		if(!$key) return $this->infoOptions;
		switch ($key){
			case '_title':
				return $this->infoOptions['pagetitle'] ?: $this->infoOptions['title'].'信息';
				break;
			default: 
				return $this->infoOptions[$key];
		}
	}
	/**
	 * 输出字段配置信息
	 * 
	 * 自动过滤不易暴露的配置
	 * @return array
	 */
	protected function packColumns(){
		foreach ($this->fieldsOptions as $k=>&$v){
			$select=array();
			if(!$v) unset($this->fieldsOptions[$k]);
			// 自动隐藏
			if(!$v['title'] && !$v['list']['title']) $v['list']['hidden']=true;
			if(!$v['title'] && !$v['form']['title']) unset($v['form']);
			// 准备选项
			if('select'==$v['list']['search']['type'] && !$v['list']['search']['options'])
				$v['list']['search']['options']= $select = call_user_func(array(&$this, "getOptions_$k"));
			if('select'==$v['form']['type'] && !isset($v['form']['options'])){
				$v['form']['options']= $select ?: (method_exists($this, "getOptions_$k") ? call_user_func(array(&$this, "getOptions_$k")) : array());
			}
			// 安全过滤
			$v=array_intersect_key($v,array('title'=>'', 'list'=>array(), 'form'=>array()));
			unset($v['list']['order'], $v['list']['callback'], $v['form']['callback'], $v['form']['validate']['server'], $v['list']['search']['sql'], $v['form']['fill']);
			$v=array_merge(array('title'=>'', 'list'=>array('search'=>false), 'form'=>false), $v);
		}
		return $this->fieldsOptions;
	}
	/**
	 * 列表页基础信息输出
	 * 
	 * 自动过滤不易暴露的配置
	 * @return array
	 */
	public function all(){
		// 安全过滤
		$pageOptions=array_intersect_key($this->pageOptions,array('add'=>'', 'limit'=>'', 'sort'=>'', 'print'=>'', 'export'=>'', 'number'=>'', 'all'=>'', 'checkbox'=>'', 'dtResponsive'=>'', 'actions'=>array(), 'tips'=>array(), 'buttons'=>array(), 'formSize'=>''));
		if(!$pageOptions['tips']) $pageOptions['tips']=false;
		$data=array(
				'title'		=>	$this->infoOptions['title'],
				'subtitle'	=>	$this->infoOptions['subtitle'],
				'options'	=>	$pageOptions,
				'columns'	=>	$this->packColumns()
		);
		return $data;
	}
	/**
	 * AJAX请求路由
	 * 
	 * 请在实现对应的 ajax_I('get.q') 方法
	 * @param string $query 请求接口
	 * @param array $json 要返回的数据，引用型传递
	 */
	public function ajax($query, &$json){
		call_user_func_array(array(&$this, 'ajax_'.$query), array(&$json));
		$json['info'] = $json['info'] ?: $this->getError();
	}
	/**
	 * 表单编辑数据响应
	 * 
	 * AJAX入口
	 * @param array $json 引用型接收
	 */
	protected function ajax_edit(&$json){
		$id = act_decrypt($en = I('pk'));
		$GLOBALS['ajaxQuery'] = 'edit';
		$GLOBALS['ajaxPk'] = $id;
		if($id) $json['val'] = $this->edit($id);
		if($json['status'] = !!$json['val']){
			$json['token'] = token_builder($en);
		}
		$json['info'] = ($json['status'] ? '请求成功！' : ($this->getError() ?: '要请求的数据不可访问！'));
		if($json['status'] && $this->pageOptions['editFields'])
			$json['columns'] = $this->packColumns();
	}
	/**
	 * 表单提交（更新）数据处理
	 * 
	 * AJAX入口
	 * @param array $json 引用型接收
	 */
	protected function ajax_update(&$json){
		if(false === ($pk = token_validator(I('_token')))) {
			$log=array('msg'=>"疑似攻击", 'info'=>'text-danger');
			Hook::listen('hy_log', $log);
			$json['info'] = '请勿非法操作，拉黑后果自负！';
			return false;
		}
		$pk = act_decrypt($pk);
		$json['status'] = !!$this->update($pk);
		$json['info'] = ($json['status'] ? '信息编辑成功！' : ($this->getError() ?: '信息编辑失败！'));
		$json['reload'] = $this->pageOptions['okRefresh'] ?: false;
	}
	/**
	 * 表单提交（新增）数据处理
	 * 
	 * AJAX入口
	 * @param array $json 引用型接收
	 */
	protected function ajax_insert(&$json){
		$json['status'] = !!$this->insert();
		$json['info'] = $json['status'] ? '数据新增成功！' : ($this->getError() ?: '数据新增失败，请检查是有有重复记录，或数据是否合法！');
		$json['reload'] = $this->pageOptions['okRefresh'] ?: false;
	}
	/**
	 * 列表删除数据处理
	 * 
	 * AJAX入口
	 * @param array $json 引用型接收
	 */
	protected function ajax_delete(&$json){
		if(!is_array($arr=explode(',', (I('pk'))))){
			$json['info']='数据非法！';
			return false;
		}
		foreach ($arr as &$v){
			$v=$id=act_decrypt($v);
		}
		$json['status']=!!$this->del($arr);
	//	$json['sql']=$this->_sql();
		$json['info']=$json['status']?'记录删除成功！' : ($this->getError() ?: '删除记录失败！');
	}
	/**
	 * 列表显示数据响应
	 * 
	 * AJAX入口
	 * @param array $json 引用型接收
	 */
	protected function ajax_list(&$json){
		$json = $this->getDtData();
	}
	/**
	 * 详情弹窗数据响应
	 * 
	 * AJAX入口
	 * @param string $type 详情请求类型（用于支持一个管理页有多种详情弹窗，默认为空）
	 */
	public function ajax_detail($type){
		$pk = act_decrypt(I('pk'));
		// 权限检查
		if(false===$this->checkPk($pk,'detail')) return false;
		$call = 'detail'.($type ? '_'.$type : '');
		return call_user_func_array(array(&$this, $call), array($pk));
	}
	/**
	 * 列表页Datatables数据输出
	 * @return array
	 */
	protected function getDtData(){
		$records = array();
		$total = $this->count(array('hy'=>true, 'scope'=>'lists'));
		$records['status'] = is_numeric($total)? true : false;
		$total = $total ?: 0;
		$log = array('msg'=>"共{$total} 条记录");
		Hook::listen('hy_log', $log);
		$limit = intval(I('length'));
		$limit = $limit < 0 ? $total : $limit;
		$offset = intval(I('start')) ?: 0;
		$draw = intval(I('draw'));
		if($draw>1) {
			// 字段排序
			$order = I('order');
			if(0 !== strpos($order['field'], '_') && false!==($_order=$this->fieldsOptions[$order['field']]['list']['order'])){
				$table = $this->fieldsOptions[$order['field']]['table'];
				$this->pageOptions['order'] = (is_string($_order) ? $_order : ($table ? $table.'.' : '').$order['field']).' '.$order['sort'];
			}
			$this->pageOptions['limit'] = "$offset,$limit";
		}
		if($this->sqlOptions['associate'] && is_string($this->pageOptions['order'])){
			$this->pageOptions['order'] = $this->fieldPre($this->pageOptions['order'], self::HYP);
		}
		$data = array();
		if($total) $data = $this->scope('lists')->order($this->pageOptions['order'])->limit($this->pageOptions['limit'])->select();
		//=============<Debug begin>===========
// 		$records['sql'] = $this->_sql();
// 		$records['select'] = $data;
// 		$records['model'] = $this;
		//=============<Debug end>=============
		$valid = array();
		foreach ($this->fieldsOptions as $k=>$v){
			if($v['title'] && false !== $v['list']['hidden']) $valid[$k] = '';
		}
		$lists = array();
		$iD = 0;
		// 方法回调
		$pk = $this->getPk();
		if($data)
		foreach ($data as $kD=>&$vD){
			$lists[$iD]['_pk'] = act_encrypt($vD[$pk]);
			foreach ($this->fieldsOptions as $kF=>$vF){
				if($tmp = $vF['list']['callback']) $lists[$iD][$kF] = $this->callbackHandler($tmp,$kF,$vD);
				else $lists[$iD][$kF] = $vD[$kF];
			}
			$vD=array_intersect_key($vD, $valid);
			$iD++;
		}
		$records["data"] = $lists;
		$records["recordsTotal"] = $total;
		$records["recordsFiltered"] = $total;
		return $records;
	}
	/**
	 * 编辑数据输出
	 * @param number $pk 要编辑记录的主键
	 */
	protected function edit($pk){
		$error = '无权访问要请求的数据！';
		$field = $this->getPk();
		if(!$field || !$pk) return $this->setError($error);
		// 数据访问检查
		$value=$this->where(array($field=>array('eq', $pk)))->find('hy');
		if(!$value) return $this->setError($error);
		// 主键检查
		if(false===$this->checkPk($pk, 'edit')) return $this->setError($error);
		$log=array('msg'=>'数据描述：'.implode('|', $value), 'style'=>'text-info');
		Hook::listen('hy_log', $log);
		// 方法回调支持
		foreach ($this->fieldsOptions as $k=>$v){
			if('file'===$v['form']['type'] && false!==$v['form']['edit'])
				$v['form']['callback']=array('file_down_url');
			if($tmp=$v['form']['callback']){
				$value[$k]=$this->callbackHandler($tmp, $k, $value);
			}
		}
		//echo $this->_sql();
		return $value;
	}
	/**
	 * 更新数据处理
	 * @param number $pk 主键 
	 */
	protected function update($pk){
		return $this->write('edit', $pk);
	}
	/**
	 * 插入数据处理
	 */
	protected function insert(){
		return $this->write('add');
	}
	/**
	 * 写入数据处理（更新、新增）
	 * @param string $type 类型：add | edit
	 * @param number $pk 主键
	 */
	protected function write($type='', $pk=null){
		if(!$type) return false;
		$data=I('post.');
		$log=array('msg'=>'数据描述：'.implode('|', $data), 'style'=>'text-info');
		Hook::listen('hy_log', $log);
		$data[$this->getPk()] = $pk;
		$this->getFieldsToValidate($type);
		$this->_validate = $this->autoValidateRules($type, $data);
		$this->autoFill($type, $data);
		/* dump($this->insertFields);
		dump($this->_validate);
		dump($this->_auto);
		dump($data);
		die;  */
		if($this->pageOptions['tablesWrite']){
			$varValid = ('add'===$type) ? 'insertFields' : 'updateFields';
			$current = $this->getTableNameHy();
			$dataList = array();
			$validFields=array();
			foreach ($this->fieldsOptions as $k=>$v){
				if(!is_array($v['form']) || false===$v['form'][$type]) continue;
				if(!$v['table'] || self::HYP === $v['table']) $v['table'] = $current;
				if($tmp=$v['form']['fill'][$type] ?: $v['form']['fill']['both']){
					$data[$k]=$this->callbackHandler($tmp, $k, $data);
				}
				if(false!==$data[$k]) {
					$dataList[$v['table']][$k] = $data[$k];
					$validFields[$v['table']][] = $k;
				}
			}
			$dataList[$current] = $this->create($dataList[$current]);
			$dataList[$current][$this->getPk()]=$pk;
			$this->$varValid = array_merge($validFields, array($current=>$this->$varValid));
			/* dump($dataList);
			dump($this->$varValid);
			die; */
			// 记录插入记录的自增值
			$rst = true;
			$ai = array();
			if('add' == $type) {
				foreach ($this->pageOptions['tablesWrite'] as $k=>$v){
					if(is_array($v)) {
						foreach ($v as $k1=>$v1)
							$dataList[$k][$k1] = $ai[$v1];
					}
					if($current==$k) $model = $this;
					else $model = M($k);
					$ai[$k]=$model->field($this->$varValid[$k])->add($dataList[$k]);
					$rst=$rst && $ai[$k];
				}
				// 如果任意表写入失败，则全部回滚
				if(!$rst){
					foreach ($ai as $k=>$v){
						if($v>1) M($k)->delete($v);
					}
				}
			}else{
				$this->pageOptions['tablesWrite'] = array_reverse($this->pageOptions['tablesWrite']);
				foreach ($this->pageOptions['tablesWrite'] as $k=>$v){
					if($current == $k) $model = $this;
					else $model = M($k);
					$ai[$k] = $model->field($this->$varValid[$k])->save($dataList[$k]) > -1;
					$rst=$rst && $ai[$k];
					if(is_array($v)){
						foreach ($v as $k1=>$v1)
							$dataList[$v1][$this->pageOptions['tablesWrite'][$v1]] = $dataList[$k][$k1];
					}
				}
			}
			// 如果任意表写入失败
			if(!$rst){
				$this->setError('多表写入处理失败！请检查数据是否有误！');
			}
			return $rst;
		}else{
			// 写入检查
			$data = $this->create($data);
			if(false === $data) return false;
			if('add' === $type){
				$dataList = array();
				foreach ($data as $k=>$v){
					if(is_array($v)) {
						foreach ($v as $k1=>$v1){
							$data[$k] = $v1;
							$dataList[] = $data;
						}
						break;
					}
				}
				if($dataList){
					$ai = array();
					$rst = true;
					foreach ($dataList as $k=>$v){
						$ai[$k] = $this->add($v);
						$rst = $rst&&($ai[$k]>0);
					}
					if(!$rst){
						$this->setError('数据新增失败！请仔细检查是否有已存在的记录！');						
						foreach ($ai as $v)
							$this->delete($v);
					}
					return $rst;
				}
				return $this->add($data);
			}else{
				$data[$this->getPk()] = $pk;
				return $this->save($data) > -1;
			}
		}
	}
	/**
	 * 删除数据
	 * 
	 * 支持逻辑删除（详见HyAllModel:pageOptions->deleteType配置）
	 * @param array $arr 要删除的主键或者主键数组
	 */
	protected function del($arr){
		$error = '无权访问要请求的数据！';
		$pk = $this->getPk();
		if(!$pk) return $this->setError($error);
		// 数据访问检查
		$value=$this->where(array($pk=>array('in', $arr)))->find('hy');
		if(!$value) return $this->setError($error);
		// 主键检查
		if(false === $this->checkPk($arr,'delete')) return $this->setError($error);
		$log=array('msg'=>'数据描述：'.implode('|', $value), 'style'=>'text-info');
		Hook::listen('hy_log', $log);
		if('delete'==$this->pageOptions['deleteType'])
			return $this->where(array($pk=>array('IN', $arr)))->delete();
		$del=explode('|', $this->pageOptions['deleteType']);
		if(2 !== count($del)) return E('deleteType配置错误！');
		return $this->where(array($pk=>array('IN', $arr), $del[0]=>array('lt', $del[1])))->save(array($del[0]=>array('exp','(`'.$this->getPk()."`+{$del[1]})")));
	}
	/**
	 * 方法回调处理
	 * 
	 * 'callback'=>array('tplReplace','{:class_id_text}','<a href="#">{0}</a>',{#}) 通过$GLOBALS['callbackKey']可直接取$key
	 * 
	 * @param string $callback
	 * @param string $key
	 * @param array $data
	 */
	private function callbackHandler($callback='', $key, $data=array()){
		// 支持多级递归callback
		if($callback['{callback}']) $callback['{callback}'] = self::callbackHandler($callback['{callback}'], $key, $data);
		// 取方法或函数名，同时整理参数数组
		$cb = array_shift($callback);
		// 如果传入'{#}'，则将自身的值放到指定位置，否则默认自身的值为第一个参数
		if(false !== $pst=array_search('{#}', $callback, true)) $callback[$pst] = $data[$key];
		else array_unshift($callback, $data[$key]);
		// 对'{:filed}'进行处理，还原成对应的值，如需多份自身的值，可传{:#}
		foreach ($callback as &$v)
			if(preg_match('/^\{:([\w\-#]+)}$/', $v, $r)){
				switch ($r[1]){
					case '#':
						$v = $data[$key];
						break;
					default:
						$v = $data[$r[1]];
				}
			}
		$GLOBALS['callbackKey'] = $key;
		// 默认 方法的优先级高于函数
		$cb = method_exists($this, 'callback_'.$cb)? array(&$this, 'callback_'.$cb) : $cb;
		return call_user_func_array($cb, $callback);
	}
	/**
	 * 动态隐藏某些字段
	 * @param string $str [fieldName],[fieldName]
	 * @param string $type list|form|add|edit
	 */
	public function setFieldsHidden($str, $type='list') {
		$arr = explode(',', $str);
		foreach ($arr as $k=>$v){
			switch($type){
				case 'list':
					$this->fieldsOptions[$v][$type]['hidden'] = true;
					break;
				case 'form':
					$this->fieldsOptions[$v]['form'] = false;
					break;
				case 'add':
					$this->fieldsOptions[$v]['form']['add'] = false;
					break;
				case 'edit':
					$this->fieldsOptions[$v]['form']['edit'] = false;
					break;
				default:
					unset($this->fieldsOptions[$v]);
 			}
		}
	}
	/**
	 * 字段值检查
	 * @param string|number $val
	 * @param string $field
	 * @param array $data
	 * @return boolean
	 */
	private function checkPk($value, $type){
		if(true===$this->pageOptions['checkPk'][$type]){
			if(false===call_user_func_array(array(&$this, '_check_pk'), array($value, $type))){
				return $this->setError('无权操作！');
			}
		}
	}
	/**
	 * 模型检索处理
	 */
	protected function searchMap() {
		if (!is_array($search = I('search')) || !$search) return;
		$log = array('msg'=>"检索");
		Hook::listen('hy_log', $log);
		$mapKey = array();
		foreach ($search as $k => $v ) {
			if(''===$v || $mapKey[$k] || !$this->fieldsOptions[$k]) continue;
			if($tmp = $this->fieldsOptions[$k]['list']['search']['sql']){
				$mapKey[$k] = true;
				$tmp=preg_replace_callback('/\{:([\w\-#]+)}/', function ($matches) use ($search){
					return $search[$matches[1]];
				}, $tmp);
				$this->addMap($tmp);
				continue;
			}
			if ($tmp=$this->fieldsOptions[$k]['list']['search']['callback']) {
				$mapKey[$k] = true;
				$this->addMap($this->callbackHandler($tmp, $k, $search));
				continue;
			}
			$field = $this->fieldsOptions[$k]['table'] ? $this->fieldsOptions[$k]['table'] . '.' . $k : "$k";
			$this->fieldsOptions[$k]['list']['search']['query']=$this->fieldsOptions[$k]['list']['search']['query'] ?: 'eq';
			switch ($query=strtolower($this->fieldsOptions[$k]['list']['search']['query'])) {
				case 'time_string' :
					$field = "UNIX_TIMESTAMP($field)";
				case 'time_stamp' :
					$start = $v ['start_time'] ? strtotime ( $v ['start_time'] ) : 0;
					$end = $v ['end_time'] ? strtotime ( $v ['end_time'] ) + 24 * 60 * 60 - 1 : 0;
					if ($start && $end && $end > $start)
						$tmp .= " and ( $field >= $start and $field <= $end)";
					elseif ($start)
						$tmp .= " and ( $field >= $start)";
					elseif ($end)
						$tmp .= " and ( $field <= $end)";
					$this->addMap($tmp);
					break;
				case 'like' :
					$this->addMap(array($field=>array('like', "%$v%")));
					break;
				default:
					$this->addMap(array($field=>array($query ?: 'eq', $v)));
					break;
			}
		}
		return ;
	}
	/**
	 * 自定义面包屑
	 */ 
	public function getBreadcrumb(){
		return $this->breadcrumb[ACTION_NAME] ?: false;
	}
	/**
	 * 表单字段有效性检查
	 * @param string $type 类型：add | edit
	 */
	protected function getFieldsToValidate($type = 'add'){
		$varValid= ('add'===$type) ? 'insertFields' : 'updateFields';
		$this->$varValid = $this->$varValid ? (is_array($this->$varValid) ? $this->$varValid : explode(',', $this->$varValid)) : array();
		if($this->$varValid) return;
		// 特殊配置处理
		$fields = array();
		foreach ($this->fieldsOptions as $k=>&$v){
			if($tmp = $v['form']['fill'])
				foreach ($tmp as $k1=>$v1)
					if('both' === $k1) $v['form']['edit']=$v['form']['add']=true;
					else $v['form'][$k1] = true;
			if(is_array($v['form']) && false !== $v['form'][$type]) {
				// 特殊类型处理
				switch (strtolower($v['form']['type'])){
					// daterange类型的form.callback保持有效
					case 'daterange':
						if($to=$v['form']['daterange']['to']){
							$fields[] = $to;
							$this->fieldsOptions[$to]=$this->fieldsOptions[$to]?:array('form'=>$v['form']);
						}
						if($from=$v['form']['daterange']['from']){
							$fields[] = $from ?: $k;
							$this->fieldsOptions[$from] = $this->fieldsOptions[$from] ?: array('form'=>$v['form']);
						}
						break;
					case 'file':
						if(!$v['form']['fill']['both']) $v['form']['fill']['both'] = array('writeFile');
						break;
					case 'select':
						if(true === $v['form']['select']['addon'] && !$v['form']['fill']['both']){
							$v['form']['fill']['both'] = array('selectAddon');
						}
						break;
				}
				// 非当前表的字段自动过滤
				if($v['table'] && !in_array($v['table'], array(self::HYP, $this->getTableNameHy()))) continue;
				$fields[] = $k;
			}
		}
		$this->$varValid = $fields;
	}
	/**
	 * 自动填充
	 * @return string|number
	 */
	private function autoFill($type,&$data){
		foreach ($this->fieldsOptions as $k=>$v){
			// 非当前表的字段自动过滤
			if($v['table'] && !in_array($v['table'], array(self::HYP, $this->getTableNameHy()))) continue;
			if(!is_array($v['form']['fill'])) continue;
			if($tmp = $v['form']['fill'][$type] ?: $v['form']['fill']['both']){
				$data[$k] = $this->callbackHandler($tmp, $k, $data);
				if(false === $data[$k]) unset($data[$k]);
			}
		}
	}
	/**
	 * 字段验证
	 * @param array $data
	 * @return array
	 */
	protected function autoValidateRules($type,$data){
		// TODO
		$rules = array();
		foreach ($this->fieldsOptions as $k=>$v){
			if($v['table'] && !in_array($v['table'], array(self::HYP, $this->getTableNameHy()))) continue;
			if(!is_array($arr=$v['form']['validate'])) continue;
			$title = $v['form']['title'] ?: $v['title'];
			foreach ($arr as $k1=>$v1){
				$when = self::MODEL_BOTH;
				if(false === $v['form']['edit'])
					$when = self::MODEL_INSERT;
				if(false === $v['form']['add'])
					$when = self::MODEL_UPDATE;
				switch ($k1){
					case 'required':
						if($v1) $rules[] = array($k, 'require', $title.'不可为空！', self::EXISTS_VALIDATE, 'regex', $when);
						break;
					case 'minlength':
						if($v1) $rules[] = array($k, "{$v1},255", $title."的长度不可小于{$v1}位！", self::VALUE_VALIDATE, 'length', $when);
						break;
					case 'number':
						if($v1) $rules[]=array($k,'number', $title.'必须为数字！', self::VALUE_VALIDATE, 'regex', $when);
				}
			}
			if(!is_array($arr = $v['form']['validate']['server'])) continue;
			foreach ($arr as $k1=>$v1){
				//$v1=array('验证回调','验证条件','提示信息');
				if(!in_array($k1, array('both',$type))) continue;
				$pass = false;
				switch ($v1[1]){
					case 'isset':
					case 'exists':
						if(isset($data[$k])) $pass = true;
						break;
					case 'must':
					case 'always':
						$pass = true;
						break;
					case 'true':
					case 'value':
					default:
						if($data[$k]) $pass = true;
						break;
				}
				if(!$pass) continue;
				if(!is_array($v1[0])) E('验证回调必须为数组格式！');
				$v1[0] = array_splice($v1[0], 1, 0, array($k, $data));
				if(false === $this->callbackHandler($v1[0], $k, $data))
					return $this->setError($v1[2] ?: "[{$title}]输入不合法！");
			}
			return $rules;
		}
	}
	/**
	 * 回调方法-选择补充
	 */
	protected function callback_selectAddon($val, $key){
		if('__FALSE__' !== I('_addon_'.$key)){
			$this->pageOptions['okRefresh'] = true;
		}
		return $val;
	}
	/**
	 * 回调方法-文件上传数据写入
	 */
	protected function callback_writeFile($val, $key){
		if($val) return token_validator($val);
		else return false;
	}
	/**
	 * 回调方法-字段唯一性检查，支持编辑
	 * @param string $val 字段的值
	 * @param string $key 字段名称
	 * @param array $data 表单的值
	 * @param string|array $other 要验证的其他字段的值
	 * @return boolean
	 */
	protected function callback_unique($val, $key, $data, $other){
		$map = array($key=>$val);
		if('delete'!==$this->pageOptions['deleteType']){
			$del=explode('|', $this->pageOptions['deleteType']);
			$map[$del[0]]=array('neq',$del[1]);
		}
		if($data[$this->getPk()]){
			$map[$this->getPk()] = array('neq',$data[$this->getPk()]);
		}
		if($other){
			if(is_string($other)) $other = explode(',', $other);
			if(!is_array($other)) $other = array();
		}
		if($other) foreach ($other as $v) $map[$v] = $data[$v];
		return !$this->where($map)->count();
	}
	/**
	 * 回调方法 - 模板替换
	 * 替换{0}、{1}...
	 * @param string|array $val 要替换成的值
	 * @param string $tpl 模板
	 */ 
	protected function callback_tplReplace($val, $tpl){
		if(is_array($val)){
			preg_replace('/\{(\d+)\}/', function($m) use($val){
				return $val[$m[1]];
			}, $tpl);
		}
		return str_replace('{0}', $val, $tpl);
	}
	/**
	 * 回调方法 - 加密字段检索回调
	 * @param string $val
	 * @param string $field
	 * @param string $query
	 * @return string
	 */
	protected function callback_searchEncrypted($val, $field){
		if(!$field){
			$field = $GLOBALS['callbackKey'];
			if($this->fieldsOptions[$field]['table'])
				$field=$this->fieldsOptions[$field]['table'].'.'.$field;
		}
		return array($field=>val_encrypt($val));
	}
	/**
	 * 回调方法 - 检索加密的姓名
	 * @param string $val
	 * @return string
	 */
	protected function callback_searchEncryptedName($val){
		return $this->callback_searchEncrypted($val, 'user.name');
	}
	/**
	 * 回调方法 - 状态图标
	 * @param number $status
	 * @param boolean $reverse
	 * @param string $type
	 * @param string $fa1
	 * @param string $fa2
	 * @return string
	 */
	protected function callback_status($status=0, $reverse=false, $type='', $fa1='', $fa2=''){
		if($reverse) $status =! $status;
		switch ($type){
			case 'lock':
				return $status ? ('<a class="btn btn-xs green disabled"><i class="fa fa-'.($fa1 ?: 'unlock-alt').'"></i></a>') 
					: ('<a class="btn btn-xs red disabled"><i class="fa fa-'.($fa2 ?: 'lock').'"></a></span>');
			default:
				return $status ? ('<a class="btn btn-xs green disabled"><i class="fa fa-'.($fa1 ?: 'check').'"></i></span>') 
					: ('<a class="btn btn-xs red disabled"><i class="fa fa-'.($fa2 ?: 'times').'"></i></span>');
		}
	}
	/**
	 * 回调方法 - 下载文件按钮
	 * @param number $id
	 * @return string
	 */
	protected function callback_fileDown($id){
		if(!$id) return '';
		$url = file_down_url($id);
		return '<a href="'.$url.'" title="下载文件" class="btn btn-xs green"><i class="fa fa-download"></i> 下载文件</a>';
	}
	/**
	 * 回调方法 - 如果为空返回第二个参数
	 * @param string $test 要评断真假的测试条件
	 * @param string 如果为假则返回的值
	 */
	protected function callback_ifEmpty($test, $value) {
		return $test ?: $value;
	}
	/**
	 * 回调方法 - 返回第二个值
	 * @param string $null 占位参数
	 * @param string $name 要返回的值
	 * @param string $empty 如果$name为假则返回的值
	 */
	protected function callback_value($null, $name, $empty='') {
		return $name ?: $empty;
	}
	/**
	 * 回调方法 - 无HTML标签过滤
	 * @return string
	 */
	protected function callback_content(){
		return I($GLOBALS['callbackKey'], '', '');
	}
	/**
	 * select选项 - status
	 * 
	 *	'1' => '正常',
	 *	'00' => '禁用'
	 * @return array
	 */
	protected function getOptions_status() {
		return array (
				'1' => '正常',
				'00' => '禁用'
		);
	}
	/**
	 * select选项 - 是否
	 * 
	 * 	0 => '否',
	 *	1 => '是'
	 * @return array
	 */
	protected function getOptions_TF() {
		return array (
				0 => '否',
				1 => '是'
		);
	}
	/**
	 * select选项 - 性别
	 * 
	 *  '女' => '女',
	 *	'男' => '男'
	 * @return array
	 */
	protected function getOptions_sex() {
		return array (
				'女' => '女',
				'男' => '男'
		);
	}
}