#!/bin/bash

#########################################################################
#                                                                 	#
# This shell script is an example to get playsms send you back any   	#
# informations from the server.		                               	#
#                                                                 	#
# USAGE:                                                          	#
#       In playsms, click on "Add SMS command", specify a command 	#
#       tag, a command code and specify the full path of this     	#
#       script followed by ##SMSSENDER##.                         	#
#                                                                 	#
# EXAMPLE:          						  	#
#	Copy this script to /home/playsms/public_html/bin		#
#       Edit /home/playsms/public_html/bin/uname.sh, edit variables	#
#       SMS command code: UNAME                                  	#
#       SMS command exec:                                         	#
#       /home/playsms/public_html/bin/uname.sh ##SMSSENDER##      	#
#                                                                 	#
# Feel free to send us your custom script so we can publish them     	#
# on the website and include them on the next release.         		#
# Send to: scripts@playsms.org			                      	#
#                                                                 	#
#########################################################################

## Username and password of the playsms user you wants to use
L="admin"
P="admin"

##  The path to your ws.php file
W="http://localhost/~playsms/ws.php"

##  The information you wants to get back
##  eg: uname -a, uptime
M=`uname -nsr`


##  You shouldn't edit the rest of the file


##  Code to use the number of the sender
##  replacing + with %2B (urlencoded form of +)
DF=`echo $1 | sed s/+/%2B/`

##  request ws.php, returns the result to sender
lynx -dump "$W?u=$L&p=$P&ta=pv&to=$DF&msg=$M" >/dev/null 2>&1

