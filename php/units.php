<?php

	$API_URL = '';

	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		$plugin	= array(
			'plugin' =>	'units',
			'help' => 'provides unit conversion functionality',
			'contact' => 'http://shrimpworks.za.net/',
			'commands' => array(
				array(
					'command' => 'convert',
					'help' => 'convert from one unit to another. usage: convert [value] <from-unit> <to-unit>',
					'args' => 3,
					'pattern' => ''
				)
			)
		);
		echo json_encode($plugin);
	} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$request = file_get_contents('php://input');
		$q = json_decode($request, true);

		if ($q['command'] == 'convert') {
			if (!is_numeric($q['args'][0])) {
				respond('first argument should be numeric', 'https://i.imgur.com/1gnAwBI.png');
			}

			$result = json_decode(file_get_contents(sprintf('%s?value=%d&from=%s&to=%s', $API_URL, $q['args'][0], $q['args'][1], $q['args'][2])), true);

			if (isset($result['error'])) {
				respond($result['error'], 'https://i.imgur.com/1gnAwBI.png');
			} else {
				$r = sprintf('%f %s = %f %s', 
					$result['from']['value'],
					$result['from']['value'] == 1 ? $result['from']['unit']['singular'] : $result['from']['unit']['plural'],
					$result['to']['value'],
					$result['to']['value'] == 1 ? $result['to']['unit']['singular'] : $result['to']['unit']['plural']
				);

				respond($r);
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