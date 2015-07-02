#! /bin/bash

SCRIPT_PATH="`dirname \"$0\"`"              # relative
SCRIPT_PATH="`( cd \"$SCRIPT_PATH\" && pwd -P )`"  # absolutized and normalized

# network options
HTTP_SCHEME=https
HTTP_HOST=localhost

# contenta options, for testing you can place your API key as a shell environment variable
[ -z "$API_HASH" ] && API_HASH=""

[ -z "$API_HASH" ] && echo "No API hash key set" && exit 1

# command options
CURL=/usr/bin/curl

OUT=/tmp/out-$$.txt
http_code=$(${CURL} --insecure -s -w '%{http_code}' -o ${OUT} \
		${HTTP_SCHEME}://${HTTP_HOST}/contenta/Api/cron_process/${API_HASH})

echo "HTTP code "  $http_code
if [ -f $OUT ]; then
	cat $OUT
	rm $OUT
fi
