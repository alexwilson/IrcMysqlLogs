#!/usr/bin/perl
######### README
# December 5th, 2013 AD
# Basically this just logs everything from every channel the irssi client is connected to and dumps it
# into a mysql database. it's probably missing a few things (Modes, for one), but the database is a bit
# different, and logs a bit more things than the original. (Credits go to Sean O'Donnelly and his tolerance of Perl.)
# There's a PHP script a bit lower here that allows
# If you drop this in a really active channel, expect some pretty high DB sizes after awhile.
# Modes are not yet supported.
# Continued, unfortunately, without permission. 
# Sean, if you're out there, here's a fixed/butchered one for you. Contact me on IRC if you read this. 
# Report errors to DJ_Arghlex on irc.stormbit.net in #stormbit or via PM.
#
# irclogs.pl, Irssi script
use strict;
use DBI;
use Irssi;

#
# MySQL Database Connection Settings (EDIT THESE VARIABLES!)
#

my %mysql = (
	hostname 	=> 'localhost',
	username 	=> 'irclogs',
	password 	=> 'ircLogs!@#',
	db 		=> 'irclogs',
	table 		=> 'irclogs',
);


# mysql dsn connection string
my $mysql_dsn = 'DBI:mysql:'. $mysql{'db'} .':'.$mysql{'hostname'};

# variable declarations
my ($this,$quote,$sql,$str);
my ($db_conn,$db_query,$rs_row);
my ($server,$msg,$nick,$address,$target); # input message variables
my ($channel,$newnick,$oldnick,$topic,$kicker,$extra); #other input vars that are probably important. maybe. 
our ($VERSION,%IRSSI);

$VERSION = "1.0.2";

%IRSSI = (
          name        => 'irssi_logs',
          authors     => 'Originally by Sean O\'Donnel, revised, fixed, and finished by Arghlex',
          contact     => 'irc.stormbit.net @ #stormbit as DJ_Arghlex',
          url         => 'http://arghargh200.net',
          license     => 'GPL',
          description => 'MySQL-Driven Logging Script for Irssi. Not compatible with previous databases. Includes a fix for the quote-handling, and some other fixes/additions.',
          sbitems     => 'quotes',
);

sub mysql_query
{
	$sql = shift;

	if ($sql)
	{
		$db_conn = DBI->connect($mysql_dsn, $mysql{'username'}, $mysql{'password'}) 
			or return 'Connection Error: $DBI::err($DBI::errstr)';
		$db_query = $db_conn->prepare($sql) 
			or return 'SQL Error: $DBI::err($DBI::errstr)';
		$db_query->execute() 
			or return 'Query Error: $DBI::err($DBI::errstr)';
		$db_query->finish();
		$db_conn->disconnect();
	}
	return;
}

sub other_pub {
	($server,$msg,$nick,$address,$channel) = @_;
	#$server,$channel,$nick,$msg
	$msg =~ s/\'/\\\'/g;
	$sql = "INSERT INTO ". $mysql{'table'} ." (type,server,channel,nick,message) VALUES ('message','". $server->{address} ."','". $channel ."','". $nick ."','". $msg ."')";
	mysql_query($sql);
}
sub other_pub_action {
	($server,$msg,$nick,$address,$channel) = @_;
	$msg =~ s/\'/\\\'/g;
	#"action",$server,$channel,$nick,$msg
	$sql = "INSERT INTO ". $mysql{'table'} ." (type,server,channel,nick,message) VALUES ('action','". $server->{address} ."','". $channel ."','". $nick ."','". $msg ."')";
	mysql_query($sql);
}
sub other_join {
	($server,$channel,$nick,$address) = @_;
	#"join",$server,$channel,$nick
	$sql = "INSERT INTO ". $mysql{'table'} ." (type,server,channel,nick) VALUES ('join','". $server->{address} ."','". $channel ."','". $nick ."')";
	mysql_query($sql);
}
sub other_part{
	($server,$channel,$nick,$address,$msg) = @_;
	#"part",$server,$channel,$nick,$msg
	$msg =~ s/\'/\\\'/g;
	$sql = "INSERT INTO ". $mysql{'table'} ." (type,server,channel,nick,message) VALUES ('part','". $server->{address} ."','". $channel ."','". $nick ."','". $msg ."')";
	mysql_query($sql);
}
sub other_quit{
	($server,$nick,$address,$msg) = @_;
	#"quit",$server,$nick,$msg
	$msg =~ s/\'/\\\'/g;
	$sql = "INSERT INTO ". $mysql{'table'} ." (type,server,nick,message) VALUES ('quit','". $server->{address} ."','". $nick ."','". $msg ."')";
	mysql_query($sql);
}
sub other_kick{
	($server,$channel,$nick,$kicker,$address,$msg) = @_;
	#"kick",$server,$channel,$nick,$msg,$kicker
	$msg =~ s/\'/\\\'/g;
	$sql = "INSERT INTO ". $mysql{'table'} ." (type,server,channel,nick,message,extra) VALUES ('kick','". $server->{address} ."','". $channel ."','". $nick ."','". $msg ."','". $extra ."')";
	mysql_query($sql);
}
sub other_nick{
	($server,$newnick,$oldnick,$address) = @_;
	#"nick",$server,$oldnick,$newnick
	$sql = "INSERT INTO ". $mysql{'table'} ." (type,server,channel,nick,message) VALUES ('nick','". $server->{address} ."','". $channel ."','". $nick ."','". $msg ."')";
	mysql_query($sql);
}
sub other_topic{
	($server,$channel,$topic,$nick,$address) = @_;
	#"topic",$server,$channel,$nick,$msg
	$msg =~ s/\'/\\\'/g;
	$sql = "INSERT INTO ". $mysql{'table'} ." (type,server,channel,nick,message) VALUES ('topic','". $server->{address} ."','". $channel ."','". $nick ."','". $msg ."')";
	mysql_query($sql);
}
sub own_connect{
	($server) = @_;
	#"connect",$server
	$sql = "INSERT INTO ". $mysql{'table'} ." (type,server) VALUES ('connect','". $server->{address} ."')";
	mysql_query($sql);
}
sub own_disconnect{
	($server) = @_;
	#"disconnect",$server
	$sql = "INSERT INTO ". $mysql{'table'} ." (type,server) VALUES ('disconnect','". $server->{address} ."')";
	mysql_query($sql);
}

#Irssi signal bindings
Irssi::signal_add("message public","other_pub");
Irssi::signal_add("ctcp action","other_pub_action");
Irssi::signal_add("message join","other_join");
Irssi::signal_add("message part","other_part");
Irssi::signal_add("message quit","other_quit");
Irssi::signal_add("message kick","other_kick");
Irssi::signal_add("message nick","other_nick");
Irssi::signal_add("message own_nick","other_nick");
Irssi::signal_add("message topic","other_topic");
Irssi::signal_add("server connected","own_connect");
Irssi::signal_add("server connected","own_disconnect");
