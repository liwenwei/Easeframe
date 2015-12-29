<?php 
/**
 * 系统配置文件
*/

/* 数据库配置 */
$CONFIG['system']['db'] = array(
	'db_host'           =>    'localhost',
	'db_user'           =>    'root',
	'db_password'       =>    '',
	'db_database'       =>    'app',
	'db_table_prefix'   =>    'app_',
	'db_charset'        =>    'urf8',
	'db_conn'           =>    '',           // 数据库连接标识; pconn 为长久链接，默认为即时链接
);
