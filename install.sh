#!/bin/sh

mysql=`which mysql`

#####################################################
#You need to enter your MySQL admin credentials here
#####################################################

un='root';
pw='';

$mysql -u $un --password=$pw < logentries.sql  > /dev/null 2>&1
