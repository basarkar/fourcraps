<?php
$link = mysql_connect('127.0.0.1', 'root', '');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo "Mysql connected successfully\n";
$db_selected = mysql_select_db('fourcraps', $link);
echo "*****IMPORTING NOW*******\n";
import('all_india_pin_code.csv');

mysql_close($link);

function import($filename='', $delimiter=',')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 0, $delimiter)) !== FALSE)
        {
            if(!$header){
              $header = $row;
            }
            else{
              $row = array_map('mysql_escape_string', $row);
              $data = array_combine($header, $row);
              mysql_query(sprintf("insert into pin_lat_lon(pin, officename, taluk, district, state) values(%d, '%s', '%s', '%s', '%s')", $data['pincode'], $data['officename'], $data['Taluk'], $data['Districtname'], $data['statename']));
//die('done');
            }
        }
        fclose($handle);
    }
    return $data;
}
