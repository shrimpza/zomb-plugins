<?php

	$API_URL = 'http://api.wunderground.com/api/';
    $API_KEY = '';

	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		$plugin	= array(
			'plugin' =>	'weather',
			'help' => 'provides weather forcast and current conditions',
			'contact' => 'http://shrimpworks.za.net/',
			'commands' => array(
				array(
					'command' => 'current',
					'help' => 'get the current weather conditions for the location specified',
					'args' => 0,
					'pattern' => '.+'
				),
				array(
					'command' => 'today',
					'help' => 'get a weather forcast for today, for the location specified',
					'args' => 0,
					'pattern' => '.+'
				),
				array(
					'command' => 'tonight',
					'help' => 'get a weather forcast for tonight, for the location specified',
					'args' => 0,
					'pattern' => '.+'
				),
				array(
					'command' => 'tomorrow',
					'help' => 'get a weather forcast for tomorrow, for the location specified',
					'args' => 0,
					'pattern' => '.+'
				)
			)
		);
		echo json_encode($plugin);
	} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$request = file_get_contents('php://input');
		$q = json_decode($request, true);

		$location = location($q['args'][0]);

		if ($q['command'] == 'current') {
			$conditions = json_decode(file_get_contents($API_URL . $API_KEY . '/conditions/q/' . $location . '.json'), true);

			if (isset($conditions['response']['error'])) {
				respond($conditions['response']['error']['description'], 'https://i.imgur.com/1gnAwBI.png');
			} else {
				$current = $conditions['current_observation'];
				$r = sprintf('Current conditions in %s: %s; Temperature: %s C, Feels like: %s C, Humidity: %s',
						$current['display_location']['full'], $current['weather'], $current['temp_c'], $current['feelslike_c'], $current['relative_humidity']);

				respond($r, $current['icon_url']);
			}
		} else if (($q['command'] == 'today') || ($q['command'] == 'tonight') || ($q['command'] == 'tomorrow')) {
			$forecast = json_decode(file_get_contents($API_URL . $API_KEY . '/forecast/q/' . $location . '.json'), true);

			if (isset($forecast['response']['error'])) {
				respond($forecast['response']['error']['description'], 'https://i.imgur.com/1gnAwBI.png');
			} else {
				if ($q['command'] == 'today') $dayNum = 0;
				else if ($q['command'] == 'tonight') $dayNum = 1;
				else if ($q['command'] == 'tomorrow') $dayNum = 2;

				$day = $forecast['forecast']['txt_forecast']['forecastday'][$dayNum];
				respond($day['fcttext_metric'], $day['icon_url']);
			}
		}
	}

	function location($in) {
		$location = $in;
		if (strpos($location, ',') !== false) {
			$parts = explode(',', $location);
			$location = urlencode(trim($parts[1])) . '/' .  urlencode(trim($parts[0]));
		} else {
			$location = urlencode($location);
		}
		return str_replace('+', '%20', $location);
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