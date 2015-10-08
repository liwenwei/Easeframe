<?php

class BaseController{
	
	public function __construct(){
		header('Content-type:text/html;chartset=utf-8');
	}
	
	/**
	 * 实例化模型
	 * @param string $model 模型名称
	 * @return model object
	 */
	final protected function model($model){
		if(empty($model)){
			trigger_error('不能实例化空模型');
		}
		
		$model_name = $model.'Model';
		return new $model_name;
	}
	
	/**
	 * 加载类库
	 * @param string $lib
	 * @param bool $auto
	 * @return object
	 */
	final protected function load($lib, $auto = TRUE){
		if(empty($lib)){
			trigger_error('加载类库名不能为空');
		} elseif ($auto === TRUE) {
			return Application::$_lib[$lib];
		} elseif ($auto === FALSE) {
			return Application::newLib($lib);
		}
	}
	
	
	/**
	 * 加载系统配置,默认为系统配置 $CONFIG['system'][$config]
	 * @param string  $config 配置名
	 * @return 配置文件
	 */
	final protected function config($config){
		return Application::$_config[$config];
	}
	
	
	/**
	 * 加载模板文件
	 * @param string $path 模板路径
	 * @param string $data 模板字符串
	 */
	final protected function showTemplate($path,$data = array()){
		$template =  $this->load('template');
		$template->init($path,$data);
		$template->outPut();
	}
}