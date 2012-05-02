#!/bin/bash

target="root@192.168.55.1:/var/www/html/studentlogon/"
source="OpenRoadFTP/php/"

if [[ $(ls -ld "OpenRoadFTP" | cut -c1) = 'd' ]]; then
        cd OpenRoadFTP
        git pull
        cd ..
else
        git clone git://github.com/jasononeil/OpenRoadFTP.git
fi

chmod -R 777 OpenRoadFTP/php/tmp/
rsync -av $source $target
