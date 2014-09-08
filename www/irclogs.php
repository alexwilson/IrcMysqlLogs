<?php
/**
 * IrcMysqlLogs/www/irclogs.php
 *
 * @package default
 */

$user_name = "irclogs";
$password = "rly now";
$database = "irclogs";
$dbserver = "localhost";
$table = "irclogs";

print <<<EOD
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>IRC Log Reader</title>
		<link href="/resources/bootstrap.css" rel="stylesheet">
		<style>
body {
	padding-top: 50px;
	background-color: transparent;
} 
.mainbody {
	padding: 40px 15px;
	background-color: transparent;
}
		</style>
		<link href="/style.css" rel="stylesheet">
	</head>
	<body>
		<div class="navbar navbar-default navbar-fixed-top">
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
			<div class="mainbody">
EOD;

if (empty($_GET['channel']) or empty($_GET['server'])) {
	print('<h1>Select a log!</h1><hr><a href="?channel=example&server=irc.example.net">#example on irc.example.net</a><br>Choose another!<br><form action="?" method="GET">Server:<input type="text" name="server"><br>Channel:<input type="text" name="channel"></form>');
} else {
	$db = new mysqli($dbserver, $user_name, $password, $database);

	if ($db->connect_errno)
	    die(sprintf("<h1 style='red'>Database not found or inaccessible. %s</h1>\n", $db->connect_error));

	if (!$mysqli->set_charset("utf8"))
    	die(sprintf("<h1 style='red'>Error loading character set UTF8. %s\n", $db->error));
	
	$server = mysqli_real_escape_string($_GET['server']);
	$channel = mysql_real_escape_string($_GET['channel']);
	$result = $db->query(printf("SELECT * FROM `%s` WHERE `server` = '%s' AND `channel` = '#%s' ORDER BY `logstamp` ASC", 
		addcslashes($db->real_escape_string($table), '%_'), 
		addcslashes($db->real_escape_string($server), '%_'), 
		addcslashes($db->real_escape_string($channel), '%_')
	));

	print("<h1>IRC Logs for #{$channel} on {$server}</h1><br><a href='#log-end'>End of log</a><h4><p class='lead'><tt><h5 align='left'>");

	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$row["message"] = htmlspecialchars($row["message"]);

		switch $row['type'] {
			case 'message':
				print("<font color='black'>[{$row['logstamp']}] &lt{$row['nick']}&gt {$row['message']}</font><br>");
				break;

			case 'action':
				print("<font color='hotpink'>[{$row['logstamp']}] -*- {$row['nick']} {$row['message']}</font><br>");
				break;

			case 'quit':
				print("<font color='orange'>[{$row['logstamp']}] &lt== {$row['nick']} quit IRC ({$row['message']})</font><br>");
				break;

			case 'part':
				print("<font color='yellow'>[{$row['logstamp']}] &lt-- {$row['nick']} parted IRC ({$row['message']})</font><br>");
				break;

			case 'join':
				print("<font color='limegreen'>[{$row['logstamp']}] ==&gt {$row['nick']} joined IRC {$row['message']}</font><br>");
				break;

			case 'kick':
				print("<font color='red'>[{$row['logstamp']}] &lt++ {$row['extra']} kicked {$row['nick']} (Reason: {$row['message']})</font><br>");
				break;

			case 'nick':
				print("<font color='brown'>[{$row['logstamp']}] &lt-&gt {$row['nick']} is now known as {$row['message']}</font><br>");
				break;

			case 'topic':
				print("<font color='blue'>[{$row['logstamp']}] ^^^ {$row['nick']} has changed the topic to {$row['message']}</font><br>");
				break;

			case 'connect':
				print("<font style='background-color:green;'>[{$row['logstamp']}] OOO Logger connected.</font><br>");
				break;

			case 'disconnect':
				print("<font style='background-color:red;'>[{$row['logstamp']}] XXX Logger disconnected.</font><br>");
				break;
		}

	}
	$row = null;
	$result->free();
	$db->close();
}
print <<<EOD
					<a id="log-end">
					</tt></h5></p></h4>
				</div>
			</div>
		<script src="/resources/jquery.js"></script>
		<script src="/resources/bootstrap.js"></script>
	</body>
</html>
EOD;
?>
