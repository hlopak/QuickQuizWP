#!/bin/bash

[ "$1" == "sass" ]  &&  sass --watch ./src/sass:./css 

[ "$1" == "ts" ]  &&  tsc --removeComments --outDir ./js/ -w ./src/ts/*.ts 




