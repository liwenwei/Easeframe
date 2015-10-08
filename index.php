<?php
/**
 * 应用入口文件
 * 
 */

// TODO 写个有关项目目录结构以及说明的文档

require dirname(__FILE__).'/system/app.php';
require dirname(__FILE__).'/config/config.php';

Application::run($CONFIG);