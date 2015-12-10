#!/bin/bash

echo "Paytoshi Faucet Script Builder"

echo "Please specify script version:"
read version

filename=paytoshi-faucet-v$version.zip

zip -r $filename . -x ".git*" cache\* nbproject\* ".idea*" "composer.*" coverage\* Tests\*  build.sh "paytoshi-faucet-v*"

if [ -f $FILE ];
then
    actualsize=$(wc -c "$filename" | cut -f 1 -d ' ')
	echo "File created successfully ($actualsize bytes)."
else
	echo "Unable to produce zip file."
fi
