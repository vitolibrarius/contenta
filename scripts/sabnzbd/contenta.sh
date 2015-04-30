#! /bin/bash


# debugging trace
#set -x

# 1		The final directory of the job (full path)
# 2		The name of the NZB file
# 3		Clean version of the job name (no path info and ".nzb" removed)
# 4		Newzbin report number (may be empty
# 5		Newzbin or user-defined category
# 6		Group that the NZB was posted in e.g. alt.binaries.x
usage()
{
    echo "`basename $0` [dir] [nzb] [name] [nzbid] [category] [group]"
    echo "	dir      = $DIR"
    echo "	nzb      = $NZB_FILE"
    echo "	name     = $NAME"
    echo "	nzbid    = $NZB_ID"
    echo "	category = $CATEGORY"
    echo "	group    = $GROUP"
    exit 1
}

DIR=$1
NZB_FILE=$2
NAME=$3
NZB_ID=$4
CATEGORY=$5
GROUP=$6

if [[ $# -lt 6 ]]; then
    usage
fi

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
FIND=/opt/bin/find
if [ ! -x ${FIND} ]; then
	FIND=/usr/bin/find
fi

UNRAR=/usr/syno/bin/unrar
if [ ! -x ${UNRAR} ]; then
	UNRAR=/usr/local/bin/unrar
fi

ZIP=/usr/syno/bin/zip
if [ ! -x ${ZIP} ]; then
	ZIP=/usr/bin/zip
fi

# deletable junk.  After a successful POST of the media content, the extra junk
# can be removed, and if the directory is empty, then delete it also
PURGE_LIST=($(cat <<EOF
*.nzb
*.url
*.rev
*.sfv
*.diz
*.nfo
*.DS_Store
*.exe
Thumbs.db
lude
@eaDir
EOF
))


purgeDirectory()
{
	if $PURGE_ENABLED; then
		echo "purging directory" $DIR
		for word in "${PURGE_LIST[@]}"; do
			${FIND} "$DIR" -iname "$word" -prune -print -exec rm -R {} \;
		done

		${FIND} "$DIR" -type d -empty -prune -print -exec rmdir {} \;
	fi
}

convertCBR() {
    echo "converting $1"
    if [ -f "$2" ]; then
    	rm "$2"
		if [ $? -ne 0 ]; then
			echo "Failed to remove previous zip $2"
			exit 1
		fi
	fi

	${UNRAR} t "$1" >/dev/null
	if [ $? -ne 0 ]; then
		echo "Bad RAR file"
		echo $?
		exit 1
	fi

	RAR_WORKING=${2%.*}
	if [ -d "$RAR_WORKING" ]; then
		rm -Rf "$RAR_WORKING"
		if [ $? -ne 0 ]; then
			echo "Failed to remove previous unrar $RAR_WORKING"
			exit 1
		fi
	fi

	mkdir "$RAR_WORKING"
	${UNRAR} x -r "$1" "$RAR_WORKING" >/dev/null
	if [ $? -ne 0 ]; then
		echo "Error unpacking RAR file"
		echo $?
		exit 1
	fi

	${ZIP} -r "$2" "$RAR_WORKING" >/dev/null
	if [ $? -ne 0 ]; then
		echo "Error zipping new file  $2"
		echo $?
		rm "$2"
		exit 1
	fi

	# clean up
	rm -Rf "$1" "$RAR_WORKING"
}

postComic() {
    echo "posting comic $1"
	OUT=/tmp/out-$$.txt
	http_code=$(${CURL} --insecure -s -w '%{http_code}' -o ${OUT} \
			-F "mediaFile=@$1" \
			${HTTP_SCHEME}://${HTTP_HOST}/contenta/Upload/service/${API_HASH})

	echo "HTTP code "  $http_code
	if [ -f $OUT ]; then
		cat $OUT
		rm $OUT
	fi

 	if [ $http_code == "200" ]; then
		if $PURGE_ENABLED; then
 			# success, remove media
 			rm "$1"
 		fi
 	fi
}

echo
echo "-= - - - - - - - - - - - - - - - - - - - - =-"
echo "looking for media content in '$DIR'"
PURGE_ENABLED=true
if [[ "$NZB_FILE" = "test" ]]; then
	echo "File Purge Disabled"
	PURGE_ENABLED=false
fi

${FIND} "$DIR" -type f -print |
while read file; do
	extension=$(echo "${file##*.}" | awk '{print tolower($0)}')

	case $extension in
		cbr )
			echo
			echo "----======"
			destination=${file%.*}".cbz"
			convertCBR "$file" "$destination"
			if [ -f "$destination" ]; then
				postComic "$destination"
			fi
			;;
		cbz )
			echo
			echo "----======"
			postComic "$file"
			;;
		pdf )
			# soon?
			;;
		epub )
			# soon?
			;;
		*)
			continue
			;;
	esac

done

if $PURGE_ENABLED; then
	echo
	echo "----======"
	purgeDirectory
fi
