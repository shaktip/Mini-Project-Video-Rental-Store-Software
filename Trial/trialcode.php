<html>

<head>

    <meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>Google Maps JavaScript API v3 Example: Geocoding Simple</title>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

    <script type="text/javascript">

        $(document).ready(function(){
            $("address").each(function(){
                var embed ="<iframe width='425' height='350' frameborder='0'
                scrolling='no' marginheight='0' marginwidth='0'
                src='https://maps.google.com/maps?&amp;q="+
                encodeURIComponent( $(this).text() ) +"&amp;output=embed'>
                </iframe>";
                $(this).html(embed);
            });
        });

    </script>


    <style>
        html,
        body,
        #map_canvas {
            height: 100%;
            width: 100%;
        }
    </style>
</head>
<body style="margin:0px; padding:0px;" onload="initialize()">
<div class="col-md-2">
    <address> <?php echo "gokulam, mysore";?>
    </address>
</div>
</body>
</html>

<?php

$query = @unserialize (file_get_contents('http://ip-api.com/php/'));
if ($query && $query['status'] == 'success') {
    echo 'Hey user from ' . $query['country'] . ', ' . $query['city'] . '!';
}
foreach ($query as $data) {
    echo $data . "<br>";
}

//echo $city;
echo "<br/><br/>";
$from = "pune,410040";
$to = "gokulam,mysore, 570002";
$from = urlencode($from);
$to = urlencode($to);
$data = file_get_contents("http://maps.googleapis.com/maps/api/distancematrix/json?origins=$from&destinations=$to&language=en-EN&sensor=false");
$data = json_decode($data);
$time = 0;
$distance = 0;
foreach($data->rows[0]->elements as $road) {
    $time += $road->duration->value;
    $distance += $road->distance->value;

    echo $road->duration->text;
    echo "<br/>";
    echo $road->distance->text;
    echo "<br/>";
}
echo "To: ".$data->destination_addresses[0];
echo "<br/>";
echo "From: ".$data->origin_addresses[0];
echo "<br/>";
echo "Time: ".$time." seconds";
echo "<br/>";
echo "Distance: ".$distance." meters";

?>
