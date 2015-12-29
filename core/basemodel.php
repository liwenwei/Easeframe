<?php

/**
 * @author 李文伟
 *
 */
class BaseModel{
	
	protected $db = null;
	
	
	final public function __construct(){
		header('Content-type:text/html;chartset=utf-8');
		$this->db = $this->load('mysql');
		$config_db = $this->config('db');
		
		// TODO 这里是做什么？如果是初始化数据库的字符串等信息，为什么不放在index页面，或者公共的地方，方便维护和修改
		
		//初始化数据库类
		$this->db->init(
				$config_db['db_host'],
                $config_db['db_user'],
                $config_db['db_password'],
                $config_db['db_database'],
                $config_db['db_conn'],
                $config_db['db_charset']
		);
	}
	
	/**
	 * 根据表前缀获取表名
	 * @param string $table_name 表名
	 */
	final protected function table($table_name){
		$config_db = $this->config('db');
		return $config_db['db_table_prefix'].$table_name;
	}
	
	// TODO 为什么BaseModel和BaseController中都要有方法load()、config()
	
	/**
	 * 加载类库
	 * @param string $lib
	 * @param bool $my 如果FALSE默认加载系统自动加载的类库，如果为TRUE则加载自定义类库
	 * @return Ambigous <unknown, multitype:string >|object
	 */
	final protected function load($lib,$my = FALSE){
		
		// TODO 有必要分系统类库和自定义类库吗？本身这个两个概念会让人confused
		if(empty($lib)){
			trigger_error('加载类库名不能为空');
		}elseif($my === FALSE){
			return Application::$_lib[$lib];
		}elseif($my === TRUE){
			return  Application::newLib($lib);
		}
	}
	
	/**
	 * 加载系统配置,默认为系统配置 $CONFIG['system'][$config]
	 * @param string $config
	 * @return 配置文件
	 */
	final protected function config($config = ''){
		return Application::$_config[$config];
	}
}