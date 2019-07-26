#!/bin/bash

# for CRLF issue
sed -i 's/\r//' *.sh


dir=$(pwd)
date=$(date '+%Y-%m-%d')

sudo chmod 777 -R $(pwd)/data

cp $(pwd)/data  $(pwd)/$date

cd $(pwd)/data
find . -name "*.html" -exec rm -rf {} \;
find . -name "*.json" -exec rm -rf {} \;

cd $dir


# go to script
cd $dir/script

filename=$(basename "$0")

php get-contents.php

print "content fetch done"

php get-property-info.php

python feed.py

python process-feed.py

echo "done"

