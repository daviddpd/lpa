#!/bin/sh
. /usr/local/etc/ansible-env.sh

for site in sjc1 iad1; do
	for host in util1 util2 util3; do
		echo "${host}.${site} : "
		./checkUser.php --json --user="${host}.${site}" --server="${host}.${site}.care2.com" --attr=title --update
		sleep 5
	done
done
