#!/bin/sh

[ "$IFACE" != "eth0.4094" ] || exit 0

su -c '/usr/bin/php /home/pfi/public_html/index.php gfast cableUnplugged' - pfi

ifconfig eth0.4094 down
echo 0 > /proc/gfast/driver_enable

exit 0
