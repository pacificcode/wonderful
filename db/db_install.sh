#!/bin/bash
# create database airport;
# create user `airport`@`localhost` identified by 'airport';
# GRANT ALL PRIVILEGES ON `airport`.* TO `airport`@`localhost`;

DB=airport
DB_USER=airport
DB_PASSWD=passwd
DATA_INFILE=airport-data.csv
DATA_OUTFILE=airport-data-unix.csv
SQL_INFILE=data.sql

echo "Processing data from input file"
tr -d '\r' < ${DATA_INFILE} | sed -E 's/("[^",]+),([^",]+")/\1###\2/g' | sed 's/,/|/g' > ${DATA_OUTFILE}
LINE_COUNT=$(( $(wc -l ${DATA_OUTFILE} | awk {'print $1'}) -1 ))

echo "Installing DB from schema file"

SCHEMA=$(cat db.sql)
mysql -u${DB_USER} -p${DB_PASSWD} airport < db.sql 2>/dev/null

echo "Preparing SQL file"
echo "INSERT INTO airport(id_airport,name,city,country,iata,icao,lat,lon,position,alt,tzone,stamp_create) VALUES" > ${SQL_INFILE}

var_ifs="${IFS}"
IFS="|"
count=0

echo "$LINE_COUNT records to process"
echo "Processing data into SQL format"
while read -r ID airport city country iata icao lat lon alt tzone
do
	if [ $count -eq 0 ]; then
		(( count=count+1 ))
		continue;
	fi

	city=$(echo $city | sed 's/###/,/g' | sed 's/\"//g')
	if [ -z "$iata" ]; then
		iata="NA"
	fi
	if [ "$icao" == "\N" ]; then
		icao="NA"
	fi

	data="(\"$ID\",\"$airport\",\"$city\",\"$country\",\"$iata\",\"$icao\",$lat,$lon,ST_GeomFromText('POINT($lat $lon)', 4326),\"$alt\",\"${tzone}\",CURRENT_TIMESTAMP)"
	if [[ "$count" -lt "$LINE_COUNT" ]]; then
		data+=","
	fi

	echo $data >> ${SQL_INFILE}

	if [[ $(( count % 300 )) == 0 ]]; then
		echo $(( LINE_COUNT-count+1 ))" records remaining."
	fi
	(( count=count+1 ))
done < ${DATA_OUTFILE}

IFS="${var_ifs}"
rm -f ${DATA_OUTFILE}
echo "Installing SQL data into database"
mysql -u${DB_USER} -p${DB_PASSWD} airport < ${SQL_INFILE} 2>/dev/null
echo "call distance_airport_to_radius(33.984305, -118.463262, 20 )\G" | mysql -u${DB_USER} -p${DB_PASSWD} airport
echo "$LINE_COUNT records inserted into database"
rm -f ${SQL_INFILE}
