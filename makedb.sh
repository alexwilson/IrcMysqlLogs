#/bin/bash
DBUSER="irclogs"
DBPASS="ircLogs!@#"

cat <<EOF > irclogs.sql
CREATE TABLE `irclogs` (
 `type` varchar(10) default NULL,
 `server` varchar(50) default NULL,
 `channel` varchar(50) NOT NULL,
 `nick` varchar(35) default NULL,
 `message` varchar(255) default NULL,
 `extra` varchar(35) default NULL,
 `id` mediumint(11) NOT NULL auto_increment,
 `logstamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
 PRIMARY KEY  (`id`),
 KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='IRC logs via Irssi';
EOF
mysqladmin -u root -p create irclogs
mysql -u root -p -D irclogs < irclogs.sql
echo 'grant all on irclogs.* to $DBUSER identified by "$DBPASS"' | mysql -u root -p
rm irclogs.sql
