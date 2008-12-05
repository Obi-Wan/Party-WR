#! /bin/bash

for x in `ls`;
do
  convert "$x" -resize 150x150\> "$x";
  echo "$x done."
done
