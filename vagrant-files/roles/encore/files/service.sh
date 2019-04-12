#!/usr/bin/env bash
### BEGIN INIT INFO
# Provides:          encore
# Required-Start:    $local_fs $network
# Required-Stop:     $local_fs
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: encore service
# Description:       Run encore service
### END INIT INFO

# Carry out specific functions when asked to by the system
case "$1" in
  start)
    /vagrant/vagrant-files/encore.sh > /dev/null 2>&1 &
  ;;
  *)
    echo "Usage: /etc/init.d/encore {start}"
    exit 1
    ;;
esac
