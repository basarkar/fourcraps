<?php
$link = mysql_connect('127.0.0.1', 'root', '');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo "Mysql connected successfully\n";
$db_selected = mysql_select_db('fourcraps', $link);
echo "*****FETCHING LAT LON NOW*******\n";
// Statewise geocode fetching
fetch_lat_lon_by_state();
//fetch_lat_lon_by_state('ODISHA');
//fetch_lat_lon_by_state('NAGALAND');

echo "\n\n\n\n\033[32m ALL COMPLETED \033[37m\r\n\n\n\n";

mysql_close($link);
function fetch_lat_lon_by_state() {
  $query = mysql_query(sprintf("select * from pin_lat_lon where lat is NULL and lon is NULL limit 0, 2200"));
  while($res = mysql_fetch_object($query)) {
    $address = "$res->officename, $res->taluk, $res->district, $res->state, India";
    $address = str_replace(' ', '+', $address);
    echo "Geocoding $address *** ";
    $geo_code = fetch_lat_lon($address);
    // Try without pin if google not able send us geocode.
    if (empty($geo_code)) {
      $address = "$res->officename, $res->taluk, $res->district, $res->state, India";
      $address = str_replace(' ', '+', $address);
      $geo_code = fetch_lat_lon($address);
    }
    if (!empty($geo_code)) {
      mysql_query(sprintf("update pin_lat_lon set lat = %s , lon = %s where pin = %d", $geo_code['lat'], $geo_code['lon'], $res->pin));
      echo "\033[32m DONE \033[37m\r\n";
    }
    else {
      echo "\033[31m ERROR \033[37m\r\n";
    }
  }
}

function fetch_lat_lon($address) {
  // Get cURL resource
  $curl = curl_init();

  curl_setopt_array($curl, array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => 'http://maps.googleapis.com/maps/api/geocode/json?address=' . $address  . '&sensor=false&region=in',
  ));

  // Send the request & save response to $resp
  $data = curl_exec($curl);
  $resp = json_decode($data, true);
  
  // Close request to clear up some resources
  curl_close($curl);
  
  $result = NULL;

  if (isset($resp['results'][0]['geometry']['location']['lat'])) {
    $result = array (
      'lat' => $resp['results'][0]['geometry']['location']['lat'],
      'lon' => $resp['results'][0]['geometry']['location']['lng'],
    );
  }
  // Sleep 100000 micro second = 100 milli second.
  usleep(100000);
  return $result;
}
