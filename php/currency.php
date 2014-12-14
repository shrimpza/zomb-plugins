<?php

	$API_URL = 'http://rate-exchange.appspot.com/currency';

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
			$result = json_decode(file_get_contents($API_URL . '?from=' . $args[2] . '&to=' . $args[3]), true);

			if (isset($result['err'])) {
				respond($result['err'], 'https://i.imgur.com/1gnAwBI.png');
			} else {
				$r = '';

				$amount = empty($args[1]) ? 1 : $args[1];
				$val = $result['rate'];

				respond(sprintf('%d %s = %f %s', $amount, $result['from'], $result['rate']*$amount, $result['to']));
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