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

# contenta options
API_HASH=""

# command options
CURL=/usr/bin/curl
FIND=/usr/bin/find

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
	echo
    echo "-= - - - - - - - - - - - - - - - - - - - - =-"
	echo "purging directory"
	for word in "${PURGE_LIST[@]}"; do
		echo " ... " $word
		${FIND} "$DIR" -iname "$word" -prune -print
# 		-exec rm -R {} \;
	done

	${FIND} "$DIR" -type d -empty -prune -print
	#-exec rmdir {} \;
}

postComic() {
	echo
    echo "-= - - - - - - - - - - - - - - - - - - - - =-"
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

# 	if [ $http_code == "200" ]; then
# 		# success, remove media
# # 		rm "$1"
# 	fi
}

echo "looking for media content in '$DIR'"
${FIND} "$DIR" -type f -print |
while read file; do
	extension=$(echo "${file##*.}" | awk '{print tolower($0)}')

	case $extension in
		cbr|cbz )
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

purgeDirectory
