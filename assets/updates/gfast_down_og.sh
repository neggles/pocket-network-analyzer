#!/bin/sh

[ "$IFACE" != "eth0.4094" ] || exit 0

ifconfig eth0.4094 down
echo 0 > /proc/gfast/driver_enable

exit 0
