#! /bin/bash

### BEGIN INIT INFO
# Provides:          mailhog
# Required-Start:    $local_fs $network
# Required-Stop:     $local_fs
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: mailhog service
# Description:       Run mailhog service
### END INIT INFO

# Carry out specific functions when asked to by the system
case "$1" in
  start)
    echo "Starting Mailhog..."
    /usr/bin/env /usr/local/bin/mailhog > /dev/null 2>&1 &
    ;;
  stop)
    echo "Stopping Foo..."
    sudo killall mailhog
    sleep 2
    ;;
  *)
    echo "Usage: /etc/init.d/mailhog {start|stop}"
    exit 1
    ;;
esac

exit 0
