<?php

//##################################################################################
//START OF CLASS
//##################################################################################

class DB {

//##################################################################################
//SAFETY FIRST (TAKEN FROM WORDPRESS)
//##################################################################################
function prepare( $query = null ) { 
	if ( is_null( $query ) )
		return;

	$args = func_get_args();
	array_shift( $args );
	// If args were passed as an array (as in vsprintf), move them up
	if ( isset( $args[0] ) && is_array($args[0]) )
		$args = $args[0];
	$query = str_replace( "'%s'", '%s', $query ); // in case someone mistakenly already singlequoted it
	$query = str_replace( '"%s"', '%s', $query ); // doublequote unquoting
	$query = preg_replace( '|(?<!%)%s|', "'%s'", $query ); // quote the strings, avoiding escaped strings like %%s
	#array_walk( $args, array( &$this, 'escape_by_ref' ) );
	return @vsprintf( $query, $args );
}
//##################################################################################
//LOG ERROR
//##################################################################################
  function error($e)
  {
	global $UTIL;

	$this->log($e);

  }

//##################################################################################
//ESCAPE THE STRING
//##################################################################################
  function escape($s)
  {

	$mysqli = $this->connect();
	$s = $mysqli->real_escape_string($s);
	$this->disconnect($mysqli);
	return $s;

  }

//##################################################################################
//EXECUTE A QUERY
//##################################################################################
  function query($q)
  {

	$mysqli = $this->connect();
	$result = $mysqli->query($this->prepare($q));
	if ($mysqli->error) $this->error('"' . $q . '" ' . $mysqli->error);
	$id = $mysqli->insert_id;
	#$this->disconnect();
	return $id;

  }

//##################################################################################
//GET VALUES
//##################################################################################
  function get_results($q)
  {

	$results = array();

	$mysqli = $this->connect();
	$result = $mysqli->query($q);
	if ($mysqli->error) $this->error('"' . $q . '" ' . $mysqli->error);
	#$this->disconnect();


	if ($result) {
		while ($a = $result->fetch_assoc()) {
			$r = (object) $a;
			$results[] = $r;		
		}
		return $results;
	} else {
		return;
	}

  }

//##################################################################################
//GET ROW
//##################################################################################
  function get_result($q)
  {

	$results = array();

	$mysqli = $this->connect();
	$result = $mysqli->query($q);
	if ($mysqli->error) $this->error('"' . $q . '" ' . $mysqli->error);
	#$this->disconnect();

	if ($result->num_rows > 0) {
		$r = (object) $result->fetch_assoc();
		return $r;
	} else {
		return false;
	}

  }

//##################################################################################
//GET VALUE
//##################################################################################
  function get_var($q)
  {

	$mysqli = $this->connect();
	$result = $mysqli->query($q);
	if ($mysqli->error) $this->error('"' . $q . '" ' . $mysqli->error);
	#$this->disconnect();

	if ($result) {

		if ($result->num_rows > 0) {
			$r = $result->fetch_array(MYSQLI_NUM);
			return $r[0];
		} else {
			return false;
		}

	} else {
		return false;
	}

  }
//##################################################################################
//GET COLUMN
//##################################################################################
  function get_col($q)
  {

	$mysqli = $this->connect();
	$result = $mysqli->query($q);
	if ($mysqli->error) $this->error('"' . $q . '" ' . $mysqli->error);
	#$this->disconnect();

	if ($result) {

		if ($result->num_rows > 0) {
			while ($a = $result->fetch_array(MYSQLI_NUM)) {
				$results[] = $a[0];
			}		
			return $results;
		} else {
			return false;
		}

	} else {
		return false;
	}

  }

//##################################################################################
//MAKES CONNECTION TO DATABASE
//##################################################################################
  function connect()
  {
        $mysqli = new mysqli($GLOBALS['dbserver'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);

        if ($mysqli->connect_errno) {
           die($mysqli->connect_error);
        }

	$mysqli->select_db($GLOBALS['dbname']);
	//$mysqli->set_charset("utf8");

	return $mysqli;

  }
//##################################################################################
//DISCONNECTS FROM DATABASE
//##################################################################################
  function disconnect($link)
  {
      mysqli_close($link);
  }
  
//##################################################################################
//WRITE TO LOG
//##################################################################################
function log($message) {

	$message = date("Y-m-d H:i:s") . " " . $_SERVER['REQUEST_URI'] . " " . $message . "\n"; 
	$log = fopen("/tmp/app.log","a");
	fwrite($log,$message);
	fclose($log);
}

//##################################################################################
//END OF CLASS
//##################################################################################
}

//##################################################################################
//CONSTRUCT
//##################################################################################

$DB = new DB();

?>
