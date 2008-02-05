# /etc/cron.d/playsms: crontab fragment for playsms

SHELL=/bin/sh
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin

# m h    dom mon dow    user    command

# playsms autosend actions
#
@hourly       root    cd /usr/share/playsms/bin ; ./cron.php hourly
@daily        root    cd /usr/share/playsms/bin ; ./cron.php daily
@weekly       root    cd /usr/share/playsms/bin ; ./cron.php weekly
@monthly      root    cd /usr/share/playsms/bin ; ./cron.php monthly
@reboot       root    cd /usr/share/playsms/bin ; ./cron.php startup

# test (every minute)
# * *    * * *  root    echo "playsms test"

