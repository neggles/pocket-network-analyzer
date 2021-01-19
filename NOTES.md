Create php7.1 repository on apt.test.com for binary-armhf distro


## Install Process of Sckipio toolset
```
tar -xvf gfast_delivery_2018-07-25.tar.gz
cd gfast_delivery_2018-07-25
sudo dpkg -i python3-sckgfast-0.1_all.deb
sudo dpkg -i sckgfastcli-0.1_armhf.deb
sudo apt-get install -f
tar -xf gfast-driver.tar.gz
KERNEL_DIR=/lib/modules/$(uname -r)/build
VLANC=${KERNEL_DIR}/net/8021q/vlan.c
echo "EXPORT_SYMBOL_GPL(register_vlan_dev);" | sudo tee --append "$VLANC"
echo "EXPORT_SYMBOL_GPL(unregister_vlan_dev);" | sudo tee --append "$VLANC"

sudo make -C /lib/modules/$(uname -r)/build M=/lib/modules/$(uname -r)/build/net/8021q modules
sudo make -C /lib/modules/$(uname -r)/build M=/lib/modules/$(uname -r)/build/net/8021q modules_install

cd gfast-driver
make -C src/ all
sudo mkdir -p /lib/modules/$(uname -r)/kernel/net/gfast
sudo cp src/gfast.ko /lib/modules/$(uname -r)/kernel/net/gfast/gfast.ko
[ -z "$(grep "^8021q$" /etc/modules)" ] && { echo "8021q"  | sudo tee --append /etc/modules ; }
[ -z "$(grep "^gfast$" /etc/modules)" ] && { echo "gfast" | sudo tee --append /etc/modules ; }
sudo depmod | grep gfast



```