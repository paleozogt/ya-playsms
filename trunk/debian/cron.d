# /etc/cron.d/playsms: crontab fragment for playsms

SHELL=/bin/sh
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin

# m h    dom mon dow    user    command

# playsms autosend actions
#
@hourly       root    /usr/share/playsms/bin/cron.sh hourly
@daily        root    /usr/share/playsms/bin/cron.sh daily
@weekly       root    /usr/share/playsms/bin/cron.sh weekly
@monthly      root    /usr/share/playsms/bin/cron.sh monthly
@reboot       root    /usr/share/playsms/bin/cron.sh startup

# test (every minute)
# * *    * * *  root    echo "playsms test"

