#!/bin/bash

indexFileSize=`wc -c /home/pfi/public_html/index.php | awk '{print $1}'`

if [ $indexFileSize -eq 0 ]
then
        echo "index.php is size 0";
        version=`git --git-dir=/home/pfi/public_html/.git describe --abbrev=0 --tags`
        git -C /home/pfi/public_html --git-dir=/home/pfi/public_html/.git checkout -f $version
else
        echo "Web directory is properly set.";
fi