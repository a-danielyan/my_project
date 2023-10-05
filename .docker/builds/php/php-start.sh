#!/bin/sh

PHP_FPM_PID='/tmp/php-fpm.pid'

wait_for_pid () {
    try=0

    while test $try -lt 35 ; do
        if [ ! -f "$1" ] ; then
            try=''
            break
        fi

        echo -n .
        try=`expr $try + 1`
        sleep 1
    done
}

function clean_up {

    echo "Killing $(cat $PHP_FPM_PID)"

    kill -QUIT `cat $PHP_FPM_PID`
    wait_for_pid $PHP_FPM_PID

    echo "Done!"

    exit 0
}

trap clean_up EXIT

nohup php-fpm --daemonize --pid $PHP_FPM_PID 2>&1 &

while true; do sleep 1; done
# ^ do nothing forever