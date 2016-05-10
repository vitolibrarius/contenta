#/bin/sh

SCRIPT_PATH="`dirname \"$0\"`"              # relative
SCRIPT_PATH="`( cd \"$SCRIPT_PATH\" && pwd -P )`"  # absolutized and normalized

PHPUNIT_TEST_PATH="`dirname \"$SCRIPT_PATH\"`"/phpunit

PHPUNIT=/usr/local/bin/phpunit

${PHPUNIT} --bootstrap "${PHPUNIT_TEST_PATH}"/autoload.php "${PHPUNIT_TEST_PATH}"/$1
