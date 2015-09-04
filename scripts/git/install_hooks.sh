#!/bin/sh

SCRIPT_PATH="`dirname \"$0\"`"              # relative
SCRIPT_PATH="`( cd \"$SCRIPT_PATH\" && pwd -P )`"  # absolutized and normalized

CONTENTA_HOOKS="$SCRIPT_PATH"/hooks
if [ ! -d "$CONTENTA_HOOKS" ]; then
	echo "Failed to find source hooks directory in $CONTENTA_HOOKS"
	exit 1
fi

GIT_DIR=`git rev-parse --show-toplevel`/.git
if [ ! -d "$GIT_DIR" ]; then
	echo "Failed to find .git directory in $GIT_DIR"
	exit 1
fi

GIT_HOOKS="$GIT_DIR"/hooks
if [ ! -d "$GIT_HOOKS" ]; then
	echo "Failed to find .git/hooks directory in $GIT_HOOKS"
	exit 1
fi

for hook in "$CONTENTA_HOOKS"/*
do
	hook_name="$GIT_HOOKS"/`basename $hook`
	if [ -e "$hook_name" ]; then
		if [ -f "$hook_name" ]; then
			echo "Existing $hook_name"
		fi
	else
		echo "Installing $hook_name"
		ln -s "$hook" "$hook_name"
	fi
done
