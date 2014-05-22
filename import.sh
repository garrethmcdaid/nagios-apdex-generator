#!/bin/sh

if [ $# -eq 0 ]
  then
    echo "Usage: ./import [-v] nagios.log"
    exit;
fi

mysql=`which mysql`

while read line; 
	do
	line=${line/ /;}
	IFS=';' read -a array <<< "$line"
	dt=${array[0]/[/}
	dt=${dt/]/}
	if [[ "$OSTYPE" == "darwin"* ]]; 
		then
			d=`date -r $dt +"%Y-%m-%d %H:%M:%S"`
		else
			d=`date -d @$dt +"%Y-%m-%d %H:%M:%S"`
		fi
	if [[ "${array[1]}" =~ "SERVICE" ]];
		then
			sql="INSERT INTO logentries (date,seconds,service,monitor,type,detail) values ('$d',$dt,'${array[1]}','${array[2]}','${array[3]}','${array[6]}');"
			if [[ "$1" == "-v" ]]; then echo $sql; fi;
			$mysql -u nagios --password="nagios" -D nagios -e "$sql" > /dev/null 2>&1
		fi
done < ${BASH_ARGV[0]};
