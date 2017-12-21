<?php

class Test extends Model
{
	static $table = 'tests';


	public static function testing($data)
	{

		$difficult_by = Helper::issetOrError($data, 'difficult_by', 'Не указана мин сложность', true);
		$difficult_to = Helper::issetOrError($data, 'difficult_to', 'Не указана макс сложность', true);
		$iq = Helper::issetOrError($data, 'iq', 'Не указан интелект тестируемого', true);

		//генерируем вопросы
		$questions = static::generateTest($difficult_by, $difficult_to);

		$result = [];
		$passed = 0;
		foreach ($questions as $key => $question) {
			// попробуем ответить
			$pass = static::generateAnswer($iq, $question['difficult']);
			$pass && $passed++;
			$result[] = [
				'number' => $key + 1,
				'id' => $question['id'],
				'used' => $question['used'],
				'difficult' => $question['difficult'],
				'pass' => $pass
			];
			Question::update($question['id'], ['used' => $question['used'] + 1]); //обновим used
		}

		// сохраним
		static::add([
			'iq' => $iq,
			'difficult_by' => $difficult_by,
			'difficult_to' => $difficult_to,
			'result' => $passed
		]);

		return $result;
	}

	/*
	 * метод генерации теста с максимальной приближенностью к by-to
	 * */
	public static function generateTest($by = 0, $to = 100)
	{
		$avg_used = Question::get(['select' => ['AVG(used) as avg']])[0]['avg']; // Получим среднее число использования
		/*
		 * Выведем таблицу вопросов и добавим к ней поле chance
		 * chance рассчитывается во формуле
		 * ((1 - расстояние_от_диапозона / 100) + (среднее число использований / кол-во использоаний)) / 2
		 *
		 * Таким образом кол-во использований вопроса является приоритетнее тк не ограничено 0-1,
		 * */
		$questions = static::customSelect("
			SELECT
				*,
				(RAND() * (
					(
						CASE
							WHEN difficult >= $by AND difficult <= $to
							THEN 1
							WHEN difficult < $by
							THEN(1 -($by - difficult) / 100)
							ELSE(1 -(difficult - $to) / 100)
						END
					)
					+
					(
						CASE
							WHEN $avg_used = 0 OR used = 0 
							THEN $avg_used
							ELSE $avg_used / used
						END
					)
				))
				chance
			FROM
				questions
			ORDER BY
				chance DESC
			LIMIT
				0, 40
		");

		//$questions = Helper::indexByKey($questions, 'id');

		return $questions;
	}

	private static function generateAnswer($iq, $difficult)
	{
		if ($difficult * 1 === 100) {
			return false;
		}
		if ($iq * 1 === 100) {
			return true;
		}
		//iq / 15 - коэфициент, дающий отвечающему больше шансов
		/*
		 * тут на самом деле возникло много головной боли.
		 * в сумме часа 3-4 потратил как раз таки на продумывание алгоритма
		 * как можно одной формулой удовлетворить все потребности задачи
		 * и при этому сделать ее неразрывной я так придумать и не смог
		 * получившаяся формула максимально приближена к условиям
		 * */

		$chance = $iq * (100 - $difficult) / 100 * $iq / 15;
		return rand(0, 100) < $chance;
	}
}
