#!/bin/sh

# this should go in /etc/cron.monthly
when=monthly
curl "http://localhost/playsms/menu_admin.php?inc=sms_autosend&op=autosend&when=$when"

