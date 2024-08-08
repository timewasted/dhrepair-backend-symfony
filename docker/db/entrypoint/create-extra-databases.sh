#!/bin/bash

set -e
set -u

function create_user_and_database() {
	local database=$1
	echo "  Creating database '${database}'"
	mysql -u"root" -p"${MYSQL_ROOT_PASSWORD}" <<-EOSQL
	   CREATE DATABASE IF NOT EXISTS \`${database}\`;
	   GRANT ALL ON \`${database}\`.* TO '${MYSQL_USER}'@'%';
EOSQL
}

if [ -n "${MYSQL_EXTRA_DATABASES}" ]; then
	echo "Creating additional databases: ${MYSQL_EXTRA_DATABASES}"
	for db in $(echo ${MYSQL_EXTRA_DATABASES} | tr ',' ' '); do
		create_user_and_database ${db}
	done
	echo "Extra databases created"
fi
