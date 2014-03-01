#!/bin/bash
SERVICE="irssi"
PROCESS="irssi"
SERVICEHR="IrssiLogger"
case "$1" in
	start)
		cd $HOME/.irssi/
		screen -dmS irclogs irssi
		;;
	*)
		if ps ax | grep -v grep | grep $PROCESS > /dev/null
		then
			echo "$SERVICEHR is running"
			exit
		else
			echo "$SERVICEHR is not running, starting"
			screen -dmS $SERVICE $HOME/.irssi/start.sh start
		fi
		;;
esac
