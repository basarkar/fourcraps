
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
        <title>Google Maps</title>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    </head>
    <body onunload="GUnload()">

        <div id="map" style="width: 100%; height: 600px"></div>

        <noscript><b>JavaScript must be enabled in order for you to use Google Maps.</b> 
            However, it seems JavaScript is either disabled or not supported by your browser. 
            To view Google Maps, enable JavaScript by changing your browser options, and then 
            try again.
        </noscript>

        <?php
        $host = 'localhost';
        $user_name = 'root';
        $password = '';
        $database = 'fourcraps';

        $con = mysqli_connect($host, $user_name, $password, $database);
        // Check connection
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        /*$geo_data_query = 'SELECT latitude, longitude, (6380 * acos (
                            cos ( radians(42.81) )
                            * cos( radians( latitude ) )
                            * cos( radians( longitude ) - radians(-70.81) )
                            + sin ( radians(42.81) )
                            * sin( radians( latitude ) )
                          )
                          ) AS distance
                          FROM zip
                          HAVING distance < 10
                          ORDER BY distance';*/
$geo_data_query = 'select pin, officename, taluk, district, state, lat as latitude, lon as longitude from pin_lat_lon where lat>0 and lon>0';        
        if (isset($_GET['state'])) {
          $geo_data_query = sprintf("select pin, officename, taluk, district, state, lat as latitude, lon as longitude from pin_lat_lon where lat>0 and lon>0 and state='%s'", strtoupper($_GET['state']));
        }
        if (isset($_GET['pin'])) {
          $geo_data_query = sprintf("select pin, officename, taluk, district, state, lat as latitude, lon as longitude from pin_lat_lon where lat>0 and lon>0 and pin=%d", strtoupper($_GET['pin']));
        }
        $result = mysqli_query($con, $geo_data_query);


        $geo_data = array();
        $i = 0;
        ?>
        <script type="text/javascript">
            //<![CDATA[
    
            function initialize() {
                
                //var myLatlng = new google.maps.LatLng(42.81,-70.81);
                var myLatlng = new google.maps.LatLng(26.1445169,91.7362365);
                var mapOptions = {
                    zoom: 7,
                    //mapTypeId: google.maps.MapTypeId.TERRAIN,
                    center: myLatlng
                }
             // Refer this link for load multiple infor window bind   
            // http://stackoverflow.com/questions/3059044/google-maps-js-api-v3-simple-multiple-marker-example     
           function bindInfoWindow(marker, map, infowindow, html, Ltitle) { 
            google.maps.event.addListener(marker, 'mouseover', function() {
                    infowindow.setContent(html); 
                    infowindow.open(map, marker); 

            });
            google.maps.event.addListener(marker, 'mouseout', function() {
                infowindow.close();

            }); 
        }
        
        
                var map = new google.maps.Map(document.getElementById('map'), mapOptions);
                var infowindow = new google.maps.InfoWindow();
                var marker, index;
                var contentString;
                
<?php while ($row = mysqli_fetch_array($result)) { ?>
                 
                index = <?php echo $i; ?>;
                
                
                contentString = "<?php echo addslashes($row['officename'] . ', ' . $row['taluk'] . ', ' . $row['district'] . ', ' . $row['state']. ' ' . $row['pin']); ?>";
                 
                   
              marker = new google.maps.Marker({
                     position: new google.maps.LatLng(<?php echo $row['latitude']; ?>, <?php echo $row['longitude']; ?>),
                     map: map,
                     title: 'Click Me ' + index
                   });
                       // process multiple info windows
                     bindInfoWindow(marker, map, infowindow, contentString);  

              
    <?php
    $i++;
}

mysqli_close($con);
?>
         }
     
         google.maps.event.addDomListener(window, 'load', initialize);
 
    

        </script>
    </body>

</html>




