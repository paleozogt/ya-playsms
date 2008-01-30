#!/bin/bash

shopt -s extdebug

# set up arguments
args="$1 $2 $3 --skip-column-names"
mysql="mysql"
#mysql="./test.sh"

# if this check fails, the database
# is probably empty or in a wack state
#
checkDatabase="select * from playsms_tblConfig_main limit 1;"

# Sql for checking any version later than 0.8.2
#
vercheckAny="select version from playsms_tblConfig_main where id=1;"

# In 0.8.2 and before there was no db version,
# so we have to check for a table that we know
# appeared in 0.8.2 but not before.
#
vercheck082="select * from playsms_featAutoSend limit 1;";

# first check whether the database 
# as a whole exists and has stuff in it
#
output=`$mysql $args -e "$checkDatabase" 2>/dev/null`
if [ "$?" = "0" ]; then
	# check for the db version the easy way
	#
	ver=`$mysql $args -e "$vercheckAny" 2>/dev/null`
	
	# if that failed, then we know its
	# at least 0.8.2, so use that sql
	# and if that fails, then we know its 0.8.1
	#
	if [ "$?" != "0" ]; then
	    output=`$mysql $args -e "$vercheck082" 2>/dev/null`
	    if [ "$?" = "0" ]; then
	        ver="0.8.2"
	    else
	        ver="0.8.1"
	    fi
	fi
fi

# output the db version
#
echo $ver

