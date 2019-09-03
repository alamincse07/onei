#!/bin/bash

# for CRLF issue
sed -i 's/\r//' *.sh


dir=$(pwd)
date=$(date '+%Y-%m-%d')

sudo chmod 777 -R $(pwd)/data

cp -R $(pwd)/data  $(pwd)/$date

cd $(pwd)/data

find . -name "*.html" -exec rm -rf {} \;
find . -name "*.json" -exec rm -rf {} \;

cd $dir


bash generate-feed-for-city.sh "North Vancouver"
bash generate-feed-for-city.sh "West Vancouver"

echo "Feed generation done, please upload to s3 manually"

