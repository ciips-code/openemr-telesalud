#!/bin/bash
cd /var/www/html/sites
if [ ! -d "default" ]; then
  mv ../sites_temp/* .
  rm -rf ../sites_temp
  echo "Moved sites files to volume"
fi
