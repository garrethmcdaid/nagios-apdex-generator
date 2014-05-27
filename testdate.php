<?php

date_default_timezone_set('Europe/Dublin');

echo date('U',strtotime('today')) . "\n";
echo date('U',strtotime('7 days ago 00:00')) . "\n";
echo date('U',strtotime('2014-05-15 00:00:00')) . "\n";
echo date('U',strtotime('2014-05-16 00:00:00')) . "\n";
echo date('U',strtotime('2014-05-17 00:00:00')) . "\n";

?>
