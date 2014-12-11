<?php

	$API_URL = 'http://ws.audioscrobbler.com/2.0/';
    $API_KEY = '';
    $API_LIMIT = 10;

	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		$plugin	= array(
			'plugin' =>	'lastfm',
			'help' => 'provides some simple lastfm API access',
			'contact' => 'http://shrimpworks.za.net/',
			'commands' => array(
				array(
					'command' => 'similar',
					'help' => 'find similar artists to the one provided',
					'args' => 0,
					'pattern' => '.+'
				),
				array(
					'command' => 'listening',
					'help' => 'see what a lastfm user is currently listening to',
					'args' => 1,
					'pattern' => ''
				)
			)
		);
		echo json_encode($plugin);
	} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$request = file_get_contents('php://input');
		$q = json_decode($request, true);
		
		if ($q['command'] == 'similar')	{
			$similar = json_decode(file_get_contents($API_URL . '?method=artist.getsimilar&format=json'
																. '&api_key=' . $API_KEY 
																. '&limit=' . $API_LIMIT 
																. '&artist=' . urlencode($q['args'][0])), true);

			if (isset($similar['error'])) {
				respond($similar['message'], 'https://i.imgur.com/SMQSw5C.png');
			} else {
				$r = '';
				foreach ($similar['similarartists']['artist'] as $a) {
					if (!empty($r)) $r .= ', ';
					$r .= $a['name'];
				}
				respond($r, $similar['similarartists']['artist'][0]['image'][1]['#text']);
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