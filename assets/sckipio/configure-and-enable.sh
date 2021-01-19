#!/bin/sh

# Run me at start-up after G.Fast driver(ko) was loaded
# OR Integrate me to SysV or Systemd init for auto-start

eth_iface=eth0  # name of ETH interface that will be connected to Sckipio device
vlan=4094       # VLAN for G.Fast protocol control frames - do not change VLAN Id


# G.Fast driver - is it loaded ?..
lsmod | grep gfast &>/dev/null
[ $? -ne 0 ] && { exit 1; }

# Configuring ...
vlan_iface="${eth_iface}.${vlan}"
sudo ip link add link ${eth_iface} name ${vlan_iface} type vlan id ${vlan}
sudo ifconfig ${vlan_iface} promisc up
sudo echo "${vlan_iface}" > /proc/gfast/interfaces

# Enabling ...
sudo echo 1 > /proc/gfast/driver_enable

[ $? -ne 0 ] && { exit 1; }

# Driver was successfully enabled (found G.Fast device ready for requests)
exit 0

