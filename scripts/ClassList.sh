#/bin/sh

if [ ! -d config ]; then
	if [ -d application ]; then
		cd application
	else
		echo "Unable to identify applicaiton/config directory"
		exit 1
	fi
fi


find libs -name \*.php |
	awk 'BEGIN {FS="/"} \
		{ LEN=length($NF) - 4;\
			NAME=substr($NF, 0, LEN) ;\
			if (NF == 2)  \
				printf "use \\%s as %s;\n", NAME, NAME; \
			else \
				printf "use %s\\%s as %s;\n", $2, NAME, NAME; \
		}' | sort

echo
find processor -name \*.php |
	awk 'BEGIN {FS="/"} \
		{ LEN=length($NF) - 4;\
			NAME=substr($NF, 0, LEN) ;\
			if (NF == 2)  \
				printf "use %s\\%s as %s;\n", $1, NAME, NAME; \
			else \
				printf "use %s\\%s as %s;\n", $2, NAME, NAME; \
		}' | sort

echo
find model -name \*.php |
	awk 'BEGIN {FS="/"} \
		{ LEN=length($NF) - 4;\
			NAME=substr($NF, 0, LEN) ;\
			if (NF == 2)  \
				printf "use %s\\%s as %s;\n", $1, NAME, NAME; \
			else \
				printf "use %s\\%s as %s;\n", $2, NAME, NAME; \
		}' | sort

