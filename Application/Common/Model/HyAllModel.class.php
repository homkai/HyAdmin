<?php
namespace Common\Model;
use Think\Hook;
/**
 * HyFrame数据管理框架-管理列表模型
 * @author Homkai QQ:345887894
 *
 */
abstract class HyAllModel  extends HyFrameModel{
	
	// 模型基础信息
	protected $infoOptions=array(
			'title'		=>	'',
			'subtitle'	=>	''
	);
	// 字段配置信息
	protected $fieldsOptions = array ();
	// 列表显示配置
	protected $pageOptions	= array();
	// 自定义面包屑
	protected $breadcrumb = array();
	
	protected function _initialize(){
		parent::_initialize();
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
			// 导出 - CVS,PDF
			'export'	=>	'',
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
			'checkPk'		=>	array(
					'edit'		=>	false,
					'delete'	=>	false,
					'detail'	=>	false
			),
			'detailTpl'		=>	'Common@HyFrame/detail',
			'editFields'	=>	false
		);
	}
	/**
	 * 初始化模型
	 */
	protected function _after_initialize(){
		parent::_after_initialize();
		if(!$this->tableName) $this->tableName = $this->getTableNameHy();
		$this->setInfoOptions($this->initInfoOptions());
		$this->setFieldsOptions($this->initFieldsOptions());
		$this->setPageOptions($this->initPageOptions());
		$this->setSqlOptions($this->initSqlOptions());
	}
	/**
	 * 指定模型基础配置
	 * @return array
	 */
	protected abstract function initInfoOptions();
	/**
	 * 指定列表显示配置
	 * @return array
	 */
	protected abstract function initPageOptions();
	/**
	 * 指定字段显示配置
	 * @return array
	 */
	protected abstract function initFieldsOptions();
	/**
	 * 指定SQL基础配置
	 * @return array
	 */
	protected abstract function initSqlOptions();
	/**
	 * 列表页面配置
	 * @param kvArr $options
	 */
	protected function setPageOptions($options=array(), $value=''){
		if(is_array($options)) {
			$this->pageOptions = (true===$value) ? $options : array_merge($this->pageOptions,$options);
		}elseif(is_string($options)){
			$this->pageOptions[$options]=$value;
		}
		$this->getPageOptions();
	}
	/**
	 * 获取页面配置
	 * @param string $key 
	 * 配置的键名
	 * @return multitype:array|string
	 */
	public function getPageOptions($key){
		if($key) return $this->pageOptions[$key];
		return $this->pageOptions;
	}
	/**
	 * 字段信息配置
	 * @param kvArr $options
	 */
	protected function setFieldsOptions($options=array(),$value=''){
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
	 * @param kvArr $options
	 */
	protected function setInfoOptions($options=array(),$value=''){
		if(is_array($options)) {
			$this->infoOptions = (true===$value) ? $options : array_merge($this->infoOptions,$options);
		}elseif(is_string($options)){
			$this->infoOptions[$options]=$value;
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
	 * @return twoArr
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
	 * AJAX请求模型入口
	 * 请在实现对应的 ajax_I('get._query') 方法
	 */
	public function ajaxs($query, &$json){
		call_user_func_array(array(&$this, 'ajax_'.$query), array(&$json));
		$json['info'] = $json['info'] ?: $this->getError();
	}
	/**
	 * AJAX入口
	 * @param array $json
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
	 * AJAX入口
	 * @param array $json
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
	 * AJAX入口
	 * @param array $json
	 */
	protected function ajax_insert(&$json){
		$json['status'] = !!$this->insert();
		$json['info'] = $json['status'] ? '数据新增成功！' : ($this->getError() ?: '数据新增失败，请检查是有有重复记录，或数据是否合法！');
		$json['reload'] = $this->pageOptions['okRefresh'] ?: false;
	}
	/**
	 * AJAX入口
	 * @param array $json
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
	 * AJAX入口
	 * @param array $json
	 */
	protected function ajax_list(&$json){
		$json = $this->getDtData();
	}
	/**
	 * AJAX入口
	 * @param string $pk
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
	 * @return json
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
			if(0 !== strpos($order['field'], '_')){
				$table = $this->fieldsOptions[$order['field']]['table'];
				$this->pageOptions['order'] = ($this->fieldsOptions[$order['field']]['list']['order'] ?: ($table ? $table.'.' : '').$order['field']).' '.$order['sort'];
			}
			$this->pageOptions['limit'] = "$offset,$limit";
		}
		$data = array();
		if($total) $data = $this->scope('lists')->order($this->pageOptions['order'])->limit($this->pageOptions['limit'])->select();
		//=============<Debug begin>===========
// 		dump($this);
// 		echo $th
// 		echo $this->_sql();
// 		$records['select']=dump($data);
// 		die;
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
	 * @param unknown $pk
	 */
	protected function edit($id){
		$error = '无权访问要请求的数据！';
		$pk = $this->getPk();
		if(!$pk || !$id) return $this->setError($error);
		// 数据访问检查
		$value=$this->where(array($pk=>array('eq', $id)))->find('hy');
		if(!$value) return $this->setError($error);
		// 主键检查
		if(false===$this->checkPk($id, 'edit')) return $this->setError($error);
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
	 * 插入数据处理
	 * @param string $type add | edit
	 */
	protected function write($type='',$pk=null){
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
	 * @param smpArr $pk
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
	 * @param string $callback
	 * @param string $key
	 * @param array $data
	 * @example 'callback'=>array('tplReplace','{:class_id_text}','<a href="#">{0}</a>',{#}) 通过$GLOBALS['callbackKey']可直接取$key
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
	 * @param string [fieldName],[fieldName]
	 * @param string list | form | add | edit
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
	 * @param string $type
	 */
	protected function getFieldsToValidate($type='add'){
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
	 * 回调方法-回调方法 模板替换
	 * 调换{0}
	 */ 
	protected function callback_tplReplace($val, $tpl){
		return str_replace('{0}', $val, $tpl);
	}
	/**
	 * 回调方法-加密字段检索回调
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
	 * 回调方法-检索加密的姓名
	 * @param string $val
	 * @return string
	 */
	protected function callback_searchEncryptedName($val){
		return $this->callback_searchEncrypted($val, 'user.name');
	}
	/**
	 * 回调方法-状态图标
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
	 * 回调方法-下载文件按钮
	 * @param number $id
	 * @return string
	 */
	protected function callback_fileDown($id){
		if(!$id) return '';
		$url = file_down_url($id);
		return '<a href="'.$url.'" title="下载文件" class="btn btn-xs green"><i class="fa fa-download"></i> 下载文件</a>';
	}
	/**
	 * 回调方法-如果为空返回第二个参数
	 */
	protected function callback_ifEmpty($test, $value) {
		return $test ? $test : $value;
	}
	/**
	 * 回调方法-返回第二个值
	 */
	protected function callback_value($null, $name, $empty='') {
		return $name ?: $empty;
	}
	/**
	 * 回调方法-无HTML标签过滤
	 * @return mixed
	 */
	protected function callback_content(){
		return I($GLOBALS['callbackKey'], '', '');
	}
	/**
	 * select选项 - status
	 *
	 * @return kvArr
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
	 * @return kvArr
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
	 * @return kvArr
	 */
	protected function getOptions_sex() {
		return array (
				'女' => '女',
				'男' => '男'
		);
	}
}