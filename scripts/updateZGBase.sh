#!/bin/bash

if [ -d ../app ]; then
	rm -rf zgBase/
	git clone --branch=Desenvolvimento https://github.com/DanielCassela/zgBase.git
	rm -rf zgBase/.git
	cp -af zgBase/* ../
	rm -rf zgBase/
	echo "OK"
	exit 0
else
	echo "Diretório app não encontrado "
	exit 1
fi 
git clone --branch=Desenvolvimento https://github.com/DanielCassela/zgBase.git
