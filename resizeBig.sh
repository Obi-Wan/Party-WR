#! /bin/bash

for x in `ls`;
do
  convert "$x" -resize 600x600\> "$x";
  echo "$x done."
done
