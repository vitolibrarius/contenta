#! /bin/sh

STATUS=`git status -s | wc -l`
if [ $STATUS -ne 0 ]; then
	echo "You have uncommited changes"
	echo
	git status -s
	exit 1
fi

BRANCH=`git branch | grep "*" | awk '{print $2}'`

if [ $BRANCH != "master" ]; then
	git checkout master
	git merge $BRANCH
	git branch -d $BRANCH
fi

