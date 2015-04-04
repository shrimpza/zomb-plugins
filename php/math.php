<?php

	require_once('lib/evalmath.class.php');

	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		$plugin	= array(
			'plugin' =>	'math',
			'help' => 'evaluate math expressions',
			'contact' => 'http://shrimpworks.za.net/',
			'commands' => array(
				array(
					'command' => 'calc',
					'help' => 'evaluate a given math expression and provide the answer',
					'args' => 0,
					'pattern' => ''
				)
			)
		);
		echo json_encode($plugin);

	} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$request = file_get_contents('php://input');
		$q = json_decode($request, true);
		
		if ($q['command'] == 'calc')	{
			if (empty($q['args'])) {
				$res = 'No expression provided, please provide a math expression';
			} else {
				$m = new EvalMath();
				$m->suppress_errors = true;
				$res =  $m->evaluate($q['args'][0]);
				if (!isset($res) || empty($res)) {
					$res = $m->last_error;
				} else {
					$res = $q['args'][0] . ' = ' . $res;
				}
			}

			respond($res);
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
