<?php

/*
 * настроечки,
 * */
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

//Подгружаю константы
$config = file_get_contents('./config.json');
$config = json_decode($config);

foreach ($config as $key => $value) {
	define($key, $value);
}

define('CLASS_DIR', __DIR__ . '/classes/');
define('MODEL_DIR', __DIR__ . '/models/');
define('TPL_DIR', __DIR__ . '/tpls/');


//Подключаю модельки
require_once(MODEL_DIR . 'Model.php');
require_once(MODEL_DIR . 'Question.php');
require_once(MODEL_DIR . 'Test.php');
require_once(CLASS_DIR . 'Helper.php');

//PS обычно разношу это в разные файлы, но проект небольшой пусть полежит тут
