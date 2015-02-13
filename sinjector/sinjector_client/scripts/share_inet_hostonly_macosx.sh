sudo sysctl -w net.inet.ip.forwarding=1
echo "Edit /etc/pf.confg and add the following line belowe nat-anchor:"
echo "nat on en0 from vboxnet0:network -> (en0)"
echo "Reload: sudo pfctl -f /etc/pf.conf"
echo "Enable: sudo pfctl -e"
#sudo natd -interface en0
#sudo ipfw add divert natd ip from any to any via en0
