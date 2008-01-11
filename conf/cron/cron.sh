#!/bin/sh

when=$1
curl "http://localhost/playsms/inc/feat/sms_autosend.php?op=autosend&frequency=$when"
