#!/bin/bash

for f in public/themes/*
do
	if [ -e ./$f/gulpfile.js ]
	then
		echo $f
		#npm i --prefix $f
		#npm run gulp --prefix $f
	fi
done	

for f in public/admin/themes/*
do
	#echo ./$f/gulpfile.js;
	if [ -e ./$f/gulpfile.js ]
	then
		echo $f
		#npm i --prefix $f
		#npm run gulp --prefix $f
	fi
done

sudo rm -rf storage/compiled-templates/* storage/cache/* storage/model/*/* public/page-cache/* public/assets-cache/* vvveb.zip

zip -r vvveb.zip ./ -x '*/node_modules/*' -x '.git/*' -x '.codelite/*' -x '/config/db.php'  -x '*/src/*' -x '*/scss/*' -x '*/resources/svg/*/*/*.svg'
