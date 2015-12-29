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
	 *     'id'        =>array(
	 *                 'id'  =>9,
	 *                 'fid' =>10
	 *               )
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
		// 以&分割查询字符串
		$query_orignal_arr = ! empty ( $this->url_query ['query'] ) ? explode ( '&', $this->url_query ['query'] ) : array ();
		$query_final_arr = $tmp = array ();
		if (count ( $query_orignal_arr ) > 0) {
			foreach ( $query_orignal_arr as $item ) {
				// 以=分割每个查询字符串
				$tmp = explode ( '=', $item );
				// TODO Fix the bug if the length of the array less than 2
				$query_final_arr [$tmp [0]] = $tmp [1];
			}
			if (isset ( $query_final_arr ['app'] )) {
				$this->route_url ['app'] = $query_final_arr ['app'];
				unset ( $query_final_arr ['app'] );
			}
			if (isset ( $query_final_arr ['controller'] )) {
				$this->route_url ['controller'] = $query_final_arr ['controller'];
				unset ( $query_final_arr ['controller'] );
			}
			if (isset ( $query_final_arr ['action'] )) {
				$this->route_url ['action'] = $query_final_arr ['action'];
				unset ( $query_final_arr ['action'] );
			}
			if (count ( $query_final_arr ) > 0) {
				$this->route_url ['params'] = $query_final_arr;
			}
		} else {
			$this->route_url = array ();
		}
	}
	
	/**
	 * 将path info的URL转换为数组
	 */
	private function pathInfoToArray(){
		// TODO 实现该方法
	}
	
	
}