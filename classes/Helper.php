<?php
/*
 * Вспомогательные методы
 * */
class Helper
{
	/*
	 * с js data прилетает не очень красиво. Вида
	 * [
	 *      [
	 *          'name' => id,
	 *          'value' => 3
	 *      ]
	 * ]
	 * Метод преобразует в
	 * [
	 *      'id' => 3
	 * ]
	 * */
	static function parseRequest($data) {
		$tmp = [];
		foreach ($data as $index => $value) {
			$arr_key = $value['name'];
			$tmp[$arr_key] = $value['value'];
		}
		return $tmp;
	}

	/*
	 * Что бы каждый раз не проверять вручную
	 * */
	static function issetOrError($data, $field, $error, $zeroAble = false) {
		$empty = $zeroAble ? !isset($data[$field]) || $data[$field] === '' : empty($data[$field]);
		if($empty) {
			throw new Exception($error);
		}
		return $data[$field];
	}

	/*
	 * Метод возвращает тот же массив массивов,
	 * но в качестве индексов используется значения $index дочерних массивов
	 * */
	static function indexByKey($array, $index) {
		$newArr = [];
		foreach ($array as $row) {
			$newArr[$row[$index]] = $row;
		}
		return $newArr;
	}

	/*
	 * кусочек шаблонизатора
	 * */
	public static function renderPage($page, $variables = []) {
		$file = TPL_DIR . $page . '.html';
		if (!is_file($file)) {//Проверяем существует ли файл
			echo 'Шаблон страницы ' . $page . ' не найден';
			exit;
		}
		if (filesize($file) === 0) {//Проверяем не пустой ли файл
			echo 'Шаблон страницы ' . $page . ' пустой';
			exit;
		}
		$templateContent = file_get_contents($file);
		if(!empty($variables)){//Проверяем на наличие переменных
			foreach ($variables as $key => $value) {//Для каждой переменной
				if($value !== null){//Если есть значение
					$key = '{{' . strtoupper($key) . '}}'; //изменяем вид ключа
					$templateContent = str_replace($key, $value, $templateContent);//Заменяем ключ на значение
				}
			}
		}
		return $templateContent;
	}

}
