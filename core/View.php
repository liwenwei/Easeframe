<?php

/**
 * 视图类
 * 
 * @author 李文伟
 *
 */
class View {
	
	/**
	 * 模板输出变量
	 * @var var
	 * @access protected
	 */
	protected $var = array();
	
	
	public function __set($name, $value){
		$this->assign($name, $value);
	}
	
	public function __get($name = ''){
		if ('' === $name) {
			return $this->var;
		}
		return isset($this->var[$name]) ? $this->var[$name] : false;
	}
	
	public function __isset($name) {
		return $this->get($name);
	}
	
	public function __call($name, $arguments){
		//echo "Calling object method '$name' " . implode(', ', $arguments) . '<br/>';
	}
	
	/**
	 * 给视图赋值
	 * @param mixed $name
	 * @param mixed $value
	 */
	public function assign($name, $value = ''){
		if (is_array($name)){
			$this->var = array_merge($this->var, $name);
		} else {
			$this->var[$name] = $value;
		}
	}
	
	public function display($templateFile = '', $content = ''){
		// 解析并获取模板内容
		$content = $this->fetch($templateFile, $content);
		// 输出模板内容
		$this->render($content);
	}
	
	private function render($content){
		// 输出模板文件
        echo $content;
	}
	
	public function fetch($templateFile = '', $content = ''){
		
		$templateFile = $this->parseTemplate($templateFile);
		
		// 页面缓存
		ob_start();
		// 模板阵列变量分解成为独立变量
		extract($this->var, EXTR_OVERWRITE);
		// 载入PHP模板
		include $templateFile;
		// 获取并清空缓存
        $content = ob_get_clean();
		
		// 输出模板文件
		return $content;
	}
	
	public function parseTemplate($template = ''){
		return VIEW_PATH . '/' . $template . '.php';
	}
}

?>