Irssi <-> MySQL <-> PHP/HTML Logs
=====

Requirements: MySQL, PHP, and a webserver, optimally with PHPmyadmin setup and configured.
Irssi, Perl, and GNU Screen installed.

Basically this just logs everything from every channel the irssi client is connected to and dumps it
into a mysql database. it's probably missing a few things (Modes, for one), but the database is a bit
different, and logs a bit more things than the original. 

Expect some big DB sizes after a while. One active channel for three months made my database hit 20MB.


Modes are not yet supported.

Credits go to Sean O'Donnelly and his tolerance of Perl.

Continued, unfortunately, without permission. 

Sean, if you're out there, here's a fixed/butchered one for you. Contact me on IRC if you read this. 


Report errors to DJ_Arghlex on irc.stormbit.net in #stormbit or via PM.
==========
If I'm not there, say my nick on IRC and I'll hear it.

Usage
==========

1. Copy the files into your $HOME directory:
`
makedb.sh

.irssi/

|---config

|-+-scripts/

| |-irclogs.pl

|---start.sh

www/

|---irclogs.php
`

2) Edit www/irclogs.php, makedb.sh, .irssi/irclogs.pl, and change the password set for mysql in those files.

3) Edit .irssi/config, and change or add channels/networks.
You should make note that every channel this irssi cient is in will be automatically logged no matter what. Choose wisely.

4) Run makedb.sh. It wll ask you for your database admin's password three times.

5) Run .irssi/start.sh. It will start irssi in a named and detached screen session. screen -ls and screen -x <session> to look at it. Ctrl-A then D to exit from the session.


