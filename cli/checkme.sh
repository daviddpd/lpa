#!/bin/sh
. /usr/local/etc/ansible-env.sh

p=`dirname $0`
$p/checkUser.php --json --user="${_HOST}.${_SITE}" --server="${_HOST}.${_SITE}.care2.com" --attr=title $1
