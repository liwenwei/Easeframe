<?php
/**
 * 测试控制器
 * @version     1.0
 */
class testController extends BaseController {

        public function __construct() {
        	parent::__construct();
        }

        public function index() {
                echo 'test';
        }

        public function testDb() {
                $modTest = $this->model('test');        //示例化test模型
                $databases = $modTest->testDatabases(); //调用test模型中 testDatabases()方法
                var_dump($databases);
        }
}