<?php

/**
 * 视图类
 * 
 * @author 李文伟
 *
 */
class View {
	
	public function display($view){
		$this->render($view);
	}
	
	private function render($view){
		require VIEW_PATH . '/' . $view . '.php';
	}
	
	public function fetch($content){
		
	}
}

?>