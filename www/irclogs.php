<?php
/**
 * IrcMysqlLogs/www/irclogs.php
 *
 * @package default
 */


print('<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>IRC Log Reader</title>
		<link href="/resources/bootstrap.css" rel="stylesheet">
		<style>
			body { padding-top: 50px; } .mainbody { padding: 40px 15px; text-align: center; }
		</style>
	</head>
	<body>
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="?">IRC Log Reader</a>
				</div>
				<div class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
						<li>
							<a href="?channel=example&server=irc.example.net">#example</a>
						</li>
					</ul>
				</div>
			<!--/.nav-collapse -->
			</div>
		</div>
		<div class="container">
			<div class="mainbody">');
$user_name = "irclogs";
$password = "ircLogger@))";
$database = "irclogs";
$dbserver = "localhost";
$table = "irclogs";
$db_handle = mysql_connect($dbserver, $user_name, $password);
$db_found = mysql_select_db($database, $db_handle);
if ($db_found) {
	if ( !isset($_GET['channel']) || !isset($_GET['server']) ) {
		print('<h1>Select a log!</h1><hr><a href="?channel=example&server=irc.example.net">#example on irc.example.net</a><br>Choose another!<br><form action="?" method="GET">Server:<input type="text" name="server"><br>Channel:<input type="text" name="channel"></form>');
	}
	$server = mysql_real_escape_string($_GET['server']);
	$channel = mysql_real_escape_string($_GET['channel']);
	print('<h1>IRC Logs for #'.$channel.' on '.$server.'</h1><br><a href="#log-end">End of log</a><h4><p class="lead"><tt><h5 align="left">');
	$SQL = mysql_query("SELECT * FROM `" . $table . "` WHERE `server` = '" . $server . "' AND `channel` = '#" . $channel . "' ORDER BY `logstamp` ASC");
	while ($Result = mysql_fetch_array($SQL)) {
		$Result["message"] = htmlspecialchars($Result["message"]);
		if ($Result['type'] == "message") { print('<font color="black">[' . $Result['logstamp'] . ']    &lt ' . $Result['nick'] . '&gt ' . $Result['message'] . '</font><br>'); }
		if ($Result['type'] == "action") { print('<font color="hotpink">[' . $Result['logstamp'] . '] -*- ' . $Result['nick'] . ' ' . $Result['message'] . '</font><br>'); }
		if ($Result['type'] == "quit") { print('<font color="orange">[' . $Result['logstamp'] . '] &lt== ' . $Result['nick'] . ' quit IRC (' . $Result['message'] . ')</font><br>'); }
		if ($Result['type'] == "part") { print('<font color="yellow">[' . $Result['logstamp'] . '] &lt-- ' . $Result['nick'] . ' parted IRC (' . $Result['message'] . ')</font><br>'); }
		if ($Result['type'] == "join") { print('<font color="limegreen">[' . $Result['logstamp'] . '] ==&gt ' . $Result['nick'] . ' joined IRC ' . $Result['message'] . '</font><br>'); }
		if ($Result['type'] == "kick") { print('<font color="red">[' . $Result['logstamp'] . '] &lt++ ' . $Result['extra'] . ' kicked  ' . $Result['nick'] . ' (Reason: ' . $Result['message'] . ')</font><br>'); }
		if ($Result['type'] == "nick") { print('<font color="brown">[' . $Result['logstamp'] . '] &lt-&gt ' . $Result['nick'] . ' is now known as ' . $Result['message'] . '</font><br>'); }
		if ($Result['type'] == "topic") { print('<font color="blue">[' . $Result['logstamp'] . '] ^^^ ' . $Result['nick'] . ' has changed the topic to ' . $Result['message'] . '</font><br>'); }
		if ($Result['type'] == "connect") { print('<font style="background-color:green;">[' . $Result['logstamp'] . '] OOO Logger connected.</font><br>'); }
		if ($Result['type'] == "disconnect") { print('<font style="background-color:red;">[' . $Result['logstamp'] . '] XXX Logger disconnected.</font><br>'); }
	}
	mysql_close($db_handle);
} else {
	print '<h1 style="red">Database not found or inaccessible.</h1>';
	mysql_close($db_handle);
}
print('
	<a id="log-end">
	</tt></h5></p></h4></div></div>
	<script src="/resources/jquery.js"></script>
	<script src="/resources/bootstrap.js"></script></body></html>');
?>
