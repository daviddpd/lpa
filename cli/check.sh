#!/bin/sh
. /usr/local/etc/ansible-env.sh

for site2 in sjc1 iad1; do
	for host2 in util1 util2 util3; do		
		for site in sjc1 iad1; do
			for host in util1 util2 util3; do
				echo -n "${host}.${site} : "
				./checkUser.php --json --user="${host2}.${site2}" --server="${host}.${site}.care2.com" --attr=title $1
			done
		done
	done
done
