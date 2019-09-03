#!/bin/bash

# for CRLF issue
sed -i 's/\r//' *.sh

city=$1

dir=$(pwd)

citydir=${city// /-}  

echo $citydir

 mkdir -p "data/$citydir/details-page-contents"
 mkdir -p "data/$citydir/page-contents"
 mkdir -p  "data/$citydir/properties"
# go to script
cd $dir/script

filename=$(basename "$0")


 php get-contents.php -c "$city"

 php get-property-info.php -c "$city"


echo "content fetch done"

python feed.py -c "$city"

python process-feed.py -c "$city"

echo "$city Feed generation done, please upload to s3 manually"

