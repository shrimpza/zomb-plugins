<?php

	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		$plugin	= array(
			'plugin' =>	'time',
			'help' => 'some	time functions',
			'contact' => 'http://shrimpworks.za.net/',
			'commands' => array(
				array(
					'command' => 'current',
					'help' => 'with	no arguments, returns current server time, or current time in the time zone	provided',
					'args' => 0,
					'pattern' => ''
				)
			)
		);
		echo json_encode($plugin);

	} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$request = file_get_contents('php://input');
		$q = json_decode($request, true);
		
		if ($q['command'] == 'current')	{
			if (!empty($q['args'])) {
				if (!in_array($q['args'][0], DateTimeZone::listIdentifiers())) {
					respond('Invalid timezone specified: ' . $q['args'][0]);
				}

				$tz	= new DateTimeZone($q['args'][0]);
			} else {
				$tz	= null;
			}

			$dt	= new DateTime('now', $tz);

			respond('The current time is ' . $dt->format('d/m/Y H:i'));
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
