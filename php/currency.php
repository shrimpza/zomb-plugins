<?php
	$API_URL = 'http://finance.yahoo.com/d/quotes.csv';

	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		$plugin	= array(
			'plugin' =>	'currency',
			'help' => 'provides a currency conversion utility',
			'contact' => 'http://shrimpworks.za.net/',
			'commands' => array(
				array(
					'command' => 'convert',
					'help' => 'convert from one currency to another. usage: convert [amount] <from-currency> <to-currency>',
					'args' => 0,
					'pattern' => '(\d+ )?[a-zA-Z]{3} [a-zA-Z]{3}'
				)
			)
		);
		echo json_encode($plugin);
	} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$request = file_get_contents('php://input');
		$q = json_decode($request, true);

		$pattern = '/(\d+ )?([a-zA-Z]{3}) ([a-zA-Z]{3})/';

		if (preg_match($pattern, $q['args'][0], $args) == 0) {
			respond('could not parse arguments ' . $q['args'][0], 'https://i.imgur.com/1gnAwBI.png');
		}
		
		if ($q['command'] == 'convert') {
			$result = file_get_contents($API_URL . '?f=sl1d1t1&s=' . $args[2] . $args[3] . '=X');

			$result = explode(',', $result);

			if ($result[1] == 'N/A') {
				respond('No results for query', 'https://i.imgur.com/1gnAwBI.png');
			} else {
				$r = '';

				$amount = empty($args[1]) ? 1 : $args[1];
				$val = $result[1];

				respond(sprintf('%d %s = %f %s', $amount, $args[2], $val * $amount, $args[3]));
			}
		}
	}
	
	function respond($response, $image = '', $exit = true) {
		$res = array(
			'response' => array($response),
			'image'	=> $image
		);

		echo json_encode($res);

		if ($exit) exit;
	}

?>
