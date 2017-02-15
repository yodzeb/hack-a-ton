<?php


function distance($lat1, $lon1, $lat2, $lon2, $unit) {

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
    return ($miles * 0.8684);
  } else {
    return $miles;
  }
}


function update_targets ($targets) {
  $date = new DateTime();
  $unix = $date->getTimestamp();

  foreach ($targets as $t) {
    if (distance($_SESSION["POSITION"]["lat"] , $_SESSION["POSITION"]["lon"],
		  $t["lat"], $t["lon"], "K") < 0.5) {
      // 500 meters
      $_SESSION["TARGET"][$t["name"]] = $unix;
    }
    else {
      if ($_SESSION["TARGET"][$t["name"]] == NULL ||
	  ($unix - $_SESSION["TARGET"][$t["name"]]) > 1800) {
	$_SESSION["TARGET"][$t["name"]] =0;
      }
    }
  }

  $count = 0;
  $tar = $_SESSION["TARGET"];
  foreach ($tar as $t => $val) {
    if ($val != 0) {
      $count ++ ;
    }
  }

  if ($count == sizeof($targets)) {
    $_SESSION["FLAG"] = file("../flag/flag") [0];
  }
  else {
    $_SESSION["FLAG"] = "--- No flag yet ---";
  }
}


function update_position ($probes) {
  $max=0;
  foreach ($_SESSION["RESULTS"] as $v) {
    if ($v > $max)
      $max = $v;
  }
  $new_max=40;
  
  $LatA  = $probes[0]["lat"];
  $LonA  = $probes[0]["lon"];
  $DistA = $_SESSION["RESULTS"][$probes[0]["name"]]*$new_max/$max ;
  $LatB  = $probes[1]["lat"];	   
  $LonB  = $probes[1]["lon"];
  $DistB = $_SESSION["RESULTS"][$probes[1]["name"]]*$new_max/$max ;
  $LatC  = $probes[2]["lat"];	   
  $LonC  = $probes[2]["lon"];	   
  $DistC = $_SESSION["RESULTS"][$probes[2]["name"]]*$new_max/$max ;

  //print ("../bin/position.py $LatA $LonA $DistA $LatB $LonB $DistB $LatC $LonC $DistC");
  exec ("../bin/position.py $LatA $LonA $DistA $LatB $LonB $DistB $LatC $LonC $DistC", $position) ;

  if (preg_match ("/([\d\.]+) ([\d\.]+)/", $position[0], $res)) {
    $_SESSION["POSITION"] = array("lat" => $res[1], "lon" => $res[2] );
  }
  
};


function exec_ping($probes) {

  $count = 0;
  $_SESSION["RESULTS"] = array();

  foreach ($probes as $p) {
    $ip = $p["ip"];
    if (array_key_exists('PROBING', $_SESSION) &&
	$_SESSION["PROBING"] === "tcp") {
      $res = ping_tcp_client($ip, $_SESSION["CLIENT_IP"], $p["tcp"]);
    }
    else {
      $res = ping_icmp_client($ip, $_SESSION["CLIENT_IP"]);
    }
    $_SESSION["RESULTS"][$p["name"]] = $p["lag"] + (int)$res ;
    $count++;
  }
}

function ping_tcp_client ( $ip, $client_ip, $port ) {
  $average = 0;
  $count   = 0;

  for ($i=0; $i<3; $i++) {
    $tB = microtime(true); 
    
    $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    $timeout = array('sec'=>1,'usec'=>0);
    socket_set_option($sock,SOL_SOCKET,SO_RCVTIMEO,$timeout);
    socket_set_option($sock,SOL_SOCKET,SO_SNDTIMEO,$timeout);
    socket_bind($sock, $ip);
    $fP = socket_connect($sock, $client_ip, $port);
    
    $tA = microtime(true); 

    $time_taken = round((($tA - $tB) * 1000), 0);
    if ($time_taken > 980)
      $time_taken = 0;

    //print round((($tA - $tB) * 1000))."\n";
    $average += $time_taken;
    
    $count++;
        
    socket_close($sock);
  }

  $average /= $count;
  
  if ($average < 1) {
    $average = 1;
  }
  return $average;
}


function ping_icmp_client ($source_ip, $destination_ip) {

  $average = 0;
  $count   = 0;
  $bad_count = 0;
  
  for ($i=0; $i<3; $i++) {
    exec ("ping -I $source_ip -c 1 $destination_ip -W 1", $ping_res);
    foreach ($ping_res as $line) {
      if (preg_match("/time\=([\d\.]+)\s/s", $line, $matches)) {
	$count++;
	$average+=$matches[1];
      }
    }
  }

  if ($count > 0 )
    $average /= $count;

  if ($average < 1) {
    $average = 1;
  }
  return $average;
}


function do_ping ($probes, $targets) {
  exec_ping($probes);
  update_position ($probes);
  update_targets  ($targets);
}


?>
