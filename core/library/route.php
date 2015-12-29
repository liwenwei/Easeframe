<?php

/**
 * URL 处理类
 * @author Alen Lee
 *
 */
final class Route{
	
	/**
	 * url query string
	 * @var string
	 */
	public $url_query;
	/**
	 * 定义URL的形式，1 为普通模式， 2 为PATHINFO。普通模式，index.php?c=controller&a=action&id=2；PATHINFO，index.php/controller/action/id/2
	 * @var integer
	 */
	public $url_type;
	/**
	 * @var array
	 */
	public $route_url = array();
	
	public function __construct(){
		$this->url_query = parse_url($_SERVER['REQUEST_URI']);
	}
	
	
	/**
	 * 设置URL类型
	 * @param number $url_type
	 */
	public function setUrlType($url_type = 2){
		if($url_type > 0 && $url_type <3){
			$this->url_type = $url_type;
		}else{
			trigger_error("指定的URL模式不存在！");
		}
	}
	
	
	/**
	 * 根据Url解析出相应的地址，以及查询字符串等信息。
	 * 例如localhost/myapp/index.php/app=admin&controller=index&action=edit&id=9&fid=10，会被解析为
	 * array(
	 *     'app'       =>'admin',
	 *     'controller'=>'index',
	 *     'action'    =>'edit',
	 *     'params'    =>array(
	 *                 'id'  =>9,
	 *                 'fid' =>10)
	 * )
	 */
	public function getUrlArray(){
		$this->makeUrl();
		return $this->route_url;
	}
	
	/**
	 * 根据Url的类型，相应的去解析Url地址，以及其中包含的查询字符串
	 */
	private function makeUrl(){
		switch ($this->url_type){
			case 1:
				$this->queryToArray();
				break;
			case 2:
				$this->pathInfoToArray();
				break;
			default:
				trigger_error('找不到相应的Url类型，1 为普通模式， 2 为PATHINFO。');
		}
	}
	
	/**
	 * 将query形式的URL转化为数组
	 */
	private function queryToArray() {
		
		if (!array_key_exists('query', $this->url_query)){
			return;
		}
		
		$query_parameters = $this->parseUrlQueryString($this->url_query ['query']);
		
		if (count ( $query_parameters ) > 0) {

			if (isset ( $query_parameters ['app'] )) {
				$this->route_url ['app'] = $query_parameters ['app'];
				unset ( $query_parameters ['app'] );
			}
			if (isset ( $query_parameters ['controller'] )) {
				$this->route_url ['controller'] = $query_parameters ['controller'];
				unset ( $query_parameters ['controller'] );
			}
			if (isset ( $query_parameters ['action'] )) {
				$this->route_url ['action'] = $query_parameters ['action'];
				unset ( $query_parameters ['action'] );
			}
			
			foreach ($query_parameters as $key => $value){
				if ($key != 'controller' || $key != 'controller' || $key != 'app'){
					$this->route_url ['params'][$key] = $value;
				}
			}
		} else {
			$this->route_url = array ();
		}
	}
	
	/**
	 * 将path info的URL转换为数组
	 */
	private function pathInfoToArray(){
		
		if (!array_key_exists('path', $this->url_query)){
			return;
		}
		
		if (!array_key_exists('query', $this->url_query)){
			return;
		}
		
		$query_parameters = $this->parseUrlQueryString($this->url_query ['query']);
		$path_parameters = $this->parseUrlPath($this->url_query ['path']);
		
		// 获取$path_parameters最后两个参数，最后一个是action，倒数第二个是controller
		
		$path_parameters_count = count($path_parameters);
		if ($path_parameters_count == 0){
			$this->route_url = array ();
		} else if ($path_parameters_count == 1){
			$this->route_url ['controller'] = $path_parameters[0];
		} else if ($path_parameters_count >=2){
			$this->route_url ['controller'] = $path_parameters[$path_parameters_count-2];
			$this->route_url ['action'] = $path_parameters[$path_parameters_count-1];
		}
		
		foreach ($query_parameters as $key => $value){
			if ($key != 'controller' || $key != 'controller' || $key != 'app'){
				$this->route_url ['params'][$key] = $value;
			}
		}
	}
	
	/**
	 * 解析查询字符串，
	 * 比如：
	 * id=0&name=alen
	 * ->
	 * array('id' = 0, 'name' = 'alen')
	 * 
	 * @param string $query
	 * @return array parameters array
	 */
	private function parseUrlQueryString($query){
		// TODO: 支持HTTP Get数组，例如?a[]=1&a[]=2&a[]=3（重要）
		// http://stackoverflow.com/questions/7206978/how-to-pass-an-array-via-get-in-php
		$parameter_pairs = ! empty ( $query ) ? explode ( '&', $query ) : array ();
		$parameters = array();
		
		foreach ( $parameter_pairs as $item ) {
			// 以=分割每个查询字符串
			$tmp = explode ( '=', $item );
			// TODO Fix the bug if the length of the array less than 2
			$parameters [$tmp [0]] = $tmp [1];
		}
		
		return $parameters;
	}
	
	/**
	 * 解析URL中的Path
	 * @param unknown $path
	 * @return array
	 */
	private function parseUrlPath($path){
		return preg_split('/\//', $path, -1, PREG_SPLIT_NO_EMPTY);
	}
	
	
}