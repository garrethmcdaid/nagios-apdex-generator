<?php

date_default_timezone_set('Europe/Dublin');

//VARS
$total_ok = 0;
$total_warning = 0;
$total_critical = 0;

//DATABASE
$GLOBALS['dbserver'] = '127.0.0.1';
$GLOBALS['dbname'] = 'nagios';
$GLOBALS['dbuser'] = 'root';
$GLOBALS['dbpass'] = '';


include('db.class.php');
global $DB;

if (!empty($_REQUEST)) {
	$start = $_REQUEST['start'];
	$end = $_REQUEST['end'];
	$service = $_REQUEST['service'];
	$monitor = $_REQUEST['monitor'];
} else {
	$start = $argv[1];
	$end = $argv[2];
	$service = $argv[3];
	$monitor = $argv[4];
}

$samples = number_format((date('U',strtotime($end . " 23:59:59")) - date('U',strtotime($start . " 00:00:00"))) / 300,"0",".","");
	
$q = "SELECT * FROM logentries WHERE date > '" . $start . " 00:00:00' AND date < '" . $end . " 23:59:59' AND service = '" . $service . "' AND monitor = '" . $monitor . "'";
$data = $DB->get_results($q);

//print_r($data);

$q = "SELECT * FROM logentries WHERE date < '" . $data[0]->date . "' AND service = '" . $service . "' AND monitor = '" . $monitor . "' ORDER BY date DESC LIMIT 1";
$r = $DB->get_result($q);

if (!$r) die("Insufficient data\n");

$ds = date('U',strtotime($start . " 00:00:00"));

$i = $data[0]->seconds - $ds;

switch($r->type) {
	
	case "OK":
		$total_ok = $i;
		break;
	case "WARNING":
		$total_warning = $i;
		break;
	case "CRITICAL":
		$total_critical = $i;
		break;
	
}

foreach ($data as $k => $v) {

	if ($k === 0) continue;

	$p = $k -1;
	echo "Previous key = " . $p . "\n"; 
	
	echo "Current key seconds = " . $v->seconds . "\n";
	echo "Previous key seconds = " . $data[$p]->seconds . "\n";

	
	$i = $v->seconds - $data[$p]->seconds;
	echo "Interval = " . $i . "\n";

	switch($data[$p]->type) {
		
		case "OK":
			echo "Previous type = OK\n";
			$total_ok = $total_ok + $i;
			break;
		case "WARNING":
			echo "Previous type = WARNING\n";
			$total_warning = $total_warninng + $i;
			break;
		case "CRITICAL":
			echo "Previous type = CRITICAL\n";
			$total_critical = $total_critical + $i;
			break;
		
	}
	
}

$de = date('U',strtotime($end . " 23:59:59"));

$i = $de - end($data)->seconds;

switch(end($data)->type) {
	
	case "OK":
		$total_ok = $total_ok + $i;
		break;
	case "WARNING":
		$total_warning = $total_warning + $i;
		break;
	case "CRITICAL":
		$total_critical = $total_critical + $i;
		break;
	
}



$samples_ok = number_format($total_ok/60,"0",".","")/5;
$samples_warning = number_format($total_warning/60,"0",".","")/5;
$samples_critical = number_format($total_critical/60,"0",".","")/5;

echo "SAMPLES: " . $samples . "\n";

echo "~~~~~~~~~~~~~~~~~~~\n";

echo "SATISFIED SECS: " . $total_ok . "\n";
echo "TOLERATING SECS: " . $total_warning . "\n";
echo "FRUSTRATED SECS: " . $total_critical . "\n";

echo "~~~~~~~~~~~~~~~~~~~\n";

echo "SATISFIED MINS: " . number_format($total_ok/60,"0",".",",") . "\n";
echo "TOLERATING MINS: " . number_format($total_warning/60,"0",".",",") . "\n";
echo "FRUSTRATED MINS: " . number_format($total_critical/60,"0",".",",") . "\n";

echo "~~~~~~~~~~~~~~~~~~~\n";

echo "SATISFIED SAMPLES: " . $samples_ok . "\n";
echo "TOLERATING SAMPLES: " . $samples_warning . "\n";
echo "FRUSTRATED SAMPLES: " . $samples_critical . "\n";

echo "~~~~~~~~~~~~~~~~~~~\n";

$apdex = ($samples_ok + ($samples_warning/2))/$samples;

echo "APDEX FORMULA: " . $samples_ok . " + " . "(" . $samples_warning . "/2) / " . $samples . "\n";

echo "APDEX SCORE: " . number_format($apdex,"4",".","") . "\n";


?>
