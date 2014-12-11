<?php

	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		$plugin = array(
			'name' => 'time',
			'help' => 'some time functions',
			'contact' => 'http://shrimpworks.za.net/',
			'commands' => array(
				array(
					'name' => 'current',
					'help' => 'with no arguments, returns current server time, or current time with UTC offset provided',
					'args' => 0,
					'pattern' => ''
				)
			)
		);
		echo json_encode($plugin);
	}

?>