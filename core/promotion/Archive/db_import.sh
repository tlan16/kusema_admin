BASEDIR=$(dirname $0)
FNAME=`date +"%d_%m_%Y"`
FPASSWORD=
DBNAME=kusemaadmin
DBHOST=localhost
DBUSERNAME=root
DBPASSWORD=root

echo Base Directory: $BASEDIR
echo File Name: $FNAME
echo Database Name: $DBNAME

echo DROP DATABASE IF EXISTS $DBNAME
mysql -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD -e "DROP DATABASE IF EXISTS $DBNAME;"
echo CREATE DATABASE $DBNAME DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci
mysql -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD -e "CREATE DATABASE $DBNAME DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;"

echo START: import database
mysql -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD $DBNAME < $BASEDIR/../structure.sql
mysql -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD $DBNAME < $BASEDIR/../useraccount.sql
mysql -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD $DBNAME < $BASEDIR/../person.sql
mysql -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD $DBNAME < $BASEDIR/../role.sql
mysql -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD $DBNAME < $BASEDIR/../role_useraccount.sql
mysql -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD $DBNAME < $BASEDIR/../userprofiletype.sql
mysql -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD $DBNAME < $BASEDIR/../unit.sql

echo Done
