<?php

/*
 * лучший друг любого приложения api ^^,
 * я предпочитаю так называемый Json Api
 * ниже реализован его минималистичный вариант
 * */

header('Content-Type: application/json');
require_once('./init.php');

try {
	if (empty($_REQUEST['module']) || empty($_REQUEST['method'])) {
		throw new Exception('Не указан модуль или метод');
	}
	$module = $_REQUEST['module'];
	$method = $_REQUEST['method'];

	if(!class_exists($module)) {
		throw new Exception('Неизвестный модуль');
	}

	if(!method_exists($module, $method)) {
		throw new Exception('Неизвестный метод модуля');
	}

	$data = Helper::parseRequest($_REQUEST['data'] ?? []);

	$result = $module::$method($data);
	echo json_encode(['error' => false, 'data' => $result]);
} catch (Exception $e) {
	echo json_encode(['error' => true, 'error_text' => $e->getMessage()]);
}
