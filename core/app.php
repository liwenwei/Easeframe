<?php
/**
 * 应用驱动类
 */
define ( 'SYSTEM_PATH', dirname ( __FILE__ ) );
// TODO: 用正确的方式获取ROOT_PATH
define ( 'ROOT_PATH', substr ( SYSTEM_PATH, 0, - 5 ) );
define ( 'SYS_LIB_PATH', SYSTEM_PATH . '/library' );
define ( 'APP_LIB_PATH', ROOT_PATH . '/lib' );
// define('SYS_CORE_PATH', SYSTEM_PATH.'/core');
define ( 'CONTROLLER_PATH', ROOT_PATH . '/Application/Controller' );
define ( 'MODEL_PATH', ROOT_PATH . '/Application/Model' );
define ( 'VIEW_PATH', ROOT_PATH . '/Application/View' );
// define('LOG_PATH', ROOT_PATH.'/error/');
final class Application {
	public static $_lib = null;
	public static $_config = null;
	
	/**
	 * 初始化
	 */
	public static function init() {
		self::setAutoLibs ();
		
		require SYSTEM_PATH . '/basemodel.php';
		require SYSTEM_PATH . '/basecontroller.php';
	}
	
	/**
	 * 运行程序
	 * 
	 * @param $config 配置文件        	
	 */
	public static function run() {
		
		// TODO: 有没有更好的办法去设置utf-8编码
		header ( 'Content-Type: text/html; charset=utf-8' );
		
		require dirname(__FILE__).'/Conf/config.php';
		self::$_config = $CONFIG ['system'];
		
		// TODO: Any better way to auto load the class. Wenwei Li
		self::init ();
		self::autoload ();
		
		self::$_lib ['route']->setUrlType ( self::$_config ['route'] ['url_type'] );
		$url_array = self::$_lib ['route']->getUrlArray ();
		
		self::routeParse ( $url_array );
	}
	
	/**
	 * 自动加载类库
	 */
	public static function autoload() {
		foreach ( self::$_lib as $key => $value ) {
			require (self::$_lib [$key]);
			
			$lib = ucfirst ( $key );
			self::$_lib [$key] = new $lib ();
		}
		
		// 初始化cache
		if (is_object ( self::$_lib ['cache'] )) {
			self::$_lib ['cache']->init ( ROOT_PATH . '/' . self::$_config ['cache'] ['cache_dir'], self::$_config ['cache'] ['cache_prefix'], self::$_config ['cache'] ['cache_time'], self::$_config ['cache'] ['cache_mode'] );
		}
	}
	
	/**
	 * 加载类库
	 * 
	 * @param string $class_name
	 *        	类库的名称
	 * @return object
	 */
	public static function newLib($class_name) {
		$app_lib = $sys_lib = '';
		$app_lib = APP_LIB_PATH . '/' . self::$_config ['lib'] ['prefix'] . '_' . $class_name . '.php';
		$sys_lib = SYS_LIB_PATH . '/lib_' . $class_name . '.php';
		
		// 首先去自定义类库目录（/lib/）去找，然后再去系统类库目录（/system/）找
		if (file_exists ( $app_lib )) {
			require ($app_lib);
			$class_name = ucfirst ( self::$_config ['lib'] ['prefix'] ) . ucfirst ( $class_name );
			return new $class_name ();
		} elseif (file_exists ( $sys_lib )) {
			require ($sys_lib);
			return self::$_lib ['$class_name'] = new $class_name ();
		} else {
			trigger_error ( '加载' . $class_name . '类库不存在' );
		}
	}
	
	/**
	 * 自动加载的类库
	 */
	public static function setAutoLibs() {
		self::$_lib = array (
				'route'     => SYS_LIB_PATH . '/route.php',
				'mysql'     => SYS_LIB_PATH . '/mysql.php',
				'template'  => SYS_LIB_PATH . '/template.php',
				'cache'     => SYS_LIB_PATH . '/cache.php',
				'thumbnail' => SYS_LIB_PATH . '/thumbnail.php' 
		);
	}
	
	/**
	 * 根据URL分发到Controller和Model
	 * 
	 * @param array $url_array        	
	 */
	public static function routeParse($url_array = array()) {
		$app = '';
		$controller = '';
		$action = '';
		$model = '';
		$params = '';
		$controller_file = '';
		$model_file = '';
		
		// 获取app值
		if (isset ( $url_array ['app'] )) {
			$app = $url_array ['app'];
		}

		// 如果是控制器
		if (isset ( $url_array ['controller'] )) {
			$controller = $model = ucfirst($url_array ['controller']);
			// 如果是app
			$controller_file = ($app) ? CONTROLLER_PATH . '/' . $app . '/' . $controller . 'Controller.php' : CONTROLLER_PATH . '/' . $controller . 'Controller.php';
			$model_file = ($app) ? MODEL_PATH . '/' . $app . '/' . $model . 'Model.php' : MODEL_PATH . '/' . $model . 'Model.php';
		} else {
			// 如果没有该控制器，就显示默认控制器和model
			$controller = $model = self::$_config ['route'] ['default_controller'];
			
			$controller_file = ($app) ? CONTROLLER_PATH . '/' . $app . '/' . self::$_config ['route'] ['default_controller'] . 'Controller.php' : CONTROLLER_PATH . '/' . self::$_config ['route'] ['default_controller'] . 'Controller.php';
			$model_file = ($app) ? MODEL_PATH . '/' . $app . '/' . self::$_config ['route'] ['default_controller'] . 'Model.php' : MODEL_PATH . '/' . self::$_config ['route'] ['default_controller'] . 'Model.php';
		}
		
		// 获取action
		$action = isset ( $url_array ['action'] ) ? $url_array ['action'] : self::$_config ['route'] ['default_action'];
		
		if (isset ( $url_array ['params'] )) {
			$params = $url_array ['params'];
		}
		
		if (file_exists ( $controller_file )) {
			
			if (file_exists ( $model_file )) {
				require $model_file;
			}
			
			require $controller_file;
			
			$controller = $controller . 'Controller';
			$controller = new $controller ();
			
			if ($action) {
				if (method_exists ( $controller, $action )) {
					isset ( $params ) ? $controller->$action ( $params ) : $controller->$action ();
				} else {
					die ( '控制器方法不存在' );
				}
			} else {
				die ( '控制器方法不存在' );
			}
		} else {
			die ( '控制器不存在' );
		}
	}
}
