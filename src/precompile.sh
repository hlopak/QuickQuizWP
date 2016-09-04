#!/bin/bash

echo -e "\n\
  ver.1  \n\
  Usage: \n\
    precompile.sh sass  \n\
    precompile.sh ts  \n\
"


[ "$1" == "sass" ]  &&  sass --watch ./src/sass:./css 

[ "$1" == "ts" ]  &&  tsc --removeComments --outDir ./js/ -w ./src/ts/*.ts 




