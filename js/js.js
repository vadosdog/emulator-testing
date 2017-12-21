/*
* Метод обращения к api
* */
function request(module, method, data) {
	if(typeof data === "string") {

	}
	return $.ajax({
		url: "api.php",
		type: "GET", //если будет интересно, потом объясню почему не post
		data: {
			module,
			method,
			data
		}
	});
}

function createTd(val) {
	return $('<td/>', {
		text: val
	})
}
$(document).ready(function(){
	const $test_result = $('#test-result');
	const $test_result_count = $('#test-result-count');
	const $results_table = $('#results');
	const $testing_from = $('#testing-from');
	const $difficult_by = $('#difficult_by');
	const $difficult_to = $('#difficult_to');

	/*
	* немного ограничим выбор диапазона
	* */
	$difficult_by.on('change', function (e) {
		if($difficult_by.val() * 1 > $difficult_to.val() * 1) {
			$difficult_to.val($difficult_by.val());
		}
	});
	$difficult_to.on('change', function (e) {
		if($difficult_to.val() * 1 < $difficult_by.val() * 1) {
			$difficult_by.val($difficult_to.val());
		}
	});

	/*
	* научим форму общаться через ajax
	* */
	$testing_from.on('submit', function (e) {
		e.preventDefault();
		request('Test', 'testing', $(this).serializeArray())
			.then((data) => {
			if(data.error) {
				alert(data.error_text)
				return false;
			}

			let count = 0;
			$test_result.empty().append($.map(data.data, function (question) {
				const $tr = $('<tr/>');
				$tr.append(createTd(question.number))
					.append(createTd(question.id))
					.append(createTd(question.used))
					.append(createTd(question.difficult))
					.append(createTd(question.pass));

				question.pass && count++;
				return $tr;
			}));
			$test_result_count.empty().text(count + "/40");
		})
	});


	/*
	* вклодочки. у нас же приложение ОДНОСТРАНИЧНИК!!!
	* */
	$('[data-tab]').on('click', function () {
		const $this = $(this);
		$('.tab').hide();
		$('#' + $this.data('tab')).show();
	}).first().trigger('click');


	/*
	* при переходе на вкладку результатов, подгружаем список
	* */
	$('#result-btn').on('click', function () {
		request('Test', 'get').then((data) => {
			if(data.error) {
				alert(data.error_text)
				return false;
			}
			$results_table
				.find('tbody')
				.empty()
				.append($.map(data.data, function (tr) {
					const $tr = $('<tr/>');
					$tr.append(createTd(tr.id + 1))
						.append(createTd(tr.iq))
						.append(createTd(tr.difficult_by))
						.append(createTd(tr.difficult_to))
						.append(createTd(tr.result));
					return $tr;
				}));
		})
	});
});
