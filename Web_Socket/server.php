<?php
set_time_limit(0);

require 'class.WebSocket.php';

function wsOnMessage($clientID, $message, $messageLength, $binary) {
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );

	if ($messageLength == 0) {
		$Server->wsClose($clientID);
		return;
	}

	if ( sizeof($Server->wsClients) == 1 )
		$Server->wsSend($clientID, "Jesteś jedyną osobą na serwerze.");
	else
		foreach ( $Server->wsClients as $id => $client )
			if ( $id != $clientID )
				$Server->wsSend($id, "Klient $clientID ($ip) napisał \"$message\"");
}

function wsOnOpen($clientID)
{
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );

	$Server->log( "$ip ($clientID) polaczyl sie." );

	foreach ( $Server->wsClients as $id => $client )
		if ( $id != $clientID )
			$Server->wsSend($id, "Klient $clientID ($ip) dołącza do rozmowy.");
}

function wsOnClose($clientID, $status) {
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );

	$Server->log( "$ip ($clientID) rozlaczyl sie." );

	foreach ( $Server->wsClients as $id => $client )
		$Server->wsSend($id, "Klient $clientID ($ip) opuścił chat.");
}

$Server = new PHPWebSocket();
$Server->bind('message', 'wsOnMessage');
$Server->bind('open', 'wsOnOpen');
$Server->bind('close', 'wsOnClose');
$Server->wsStartServer('127.0.0.1', 9300);

?>