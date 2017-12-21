<?php

/*
 * основной класс для моделей
 * */
class Model
{
	private function __construct() //STATIC CLASS
	{
	}

	public static $table = '';
	protected static function query ($query,$isAdd=false)
	{
		//Обычно я использую Pixie Query Builder
		//Но тут вспомнил нативные
		//Почему не PDO? не знаю
		$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		$result = mysqli_query($link,$query);
		if($isAdd){
			$result = mysqli_insert_id($link);
		}
		mysqli_close($link);
		return $result;
	}

	public static function add(array $data) {
		$fields = implode(", ", array_keys($data));
		$values = implode(", ", array_values($data));
		$table = static::$table;
		return static::query(
			"INSERT INTO $table ($fields) VALUES ($values)", true
		);
	}

	public static function update($id, array $data) {
		$table = static::$table;
		$updates = [];
		foreach ($data as $key => $value) {
			$updates[] = "$key = '$value'";
		}
		$updates = implode(', ', $updates);
		return static::query("UPDATE $table SET $updates WHERE id = $id");
	}

	public static function get(array $data = []) {
		$table = static::$table;
		$select = '*';
		if(isset($data['select']) && is_array($data['select'])) {
			$select = implode(', ', $data['select']);
		};
		$db_result = static::query(
			"SELECT $select FROM $table" //без лимитов тк пока не надо
		);
		$list = [];
		while ($row = mysqli_fetch_assoc($db_result)){
			$list[] = $row;
		}
		return $list;
	}

	/*
	 * когда обычного селекта недостаточно
	 * */
	public static function customSelect($query) {
		$result = static::query($query);
		return static::parseSelect($result);
	}

	/*
	 * маленький хелпер парсер селекта
	 * */
	protected static function parseSelect($rows) {
		$list = [];
		while ($row = mysqli_fetch_assoc($rows)){
			$list[] = $row;
		}
		return $list;
	}
}
