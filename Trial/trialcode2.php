<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <title>Google Maps AJAX + mySQL/PHP Example</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBRGXPuL5hPBuvAr2BxDxenMuUDuMXAhb4"
            type="text/javascript"></script>
    <script type="text/javascript">
        //<![CDATA[
        var map;
        var markers = [];
        var infoWindow;
        var locationSelect;

        function load() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: new google.maps.LatLng(40, -100),
                zoom: 4,
                mapTypeId: 'roadmap',
                mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}
            });
            infoWindow = new google.maps.InfoWindow();

            locationSelect = document.getElementById("locationSelect");
            locationSelect.onchange = function() {
                var markerNum = locationSelect.options[locationSelect.selectedIndex].value;
                if (markerNum != "none"){
                    google.maps.event.trigger(markers[markerNum], 'click');
                }
            };
        }

        function searchLocations() {
            var address = document.getElementById("addressInput").value;
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({address: address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    searchLocationsNear(results[0].geometry.location);
                } else {
                    alert(address + ' not found');
                }
            });
        }

        function clearLocations() {
            infoWindow.close();
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }
            markers.length = 0;

            locationSelect.innerHTML = "";
            var option = document.createElement("option");
            option.value = "none";
            option.innerHTML = "See all results:";
            locationSelect.appendChild(option);
        }

        function searchLocationsNear(center) {
            clearLocations();

            var radius = document.getElementById('radiusSelect').value;
            var searchUrl = 'phpsqlsearch_genxml.php?lat=' + center.lat() + '&lng=' + center.lng() + '&radius=' + radius;
            downloadUrl(searchUrl, function(data) {
                var xml = parseXml(data);
                var markerNodes = xml.documentElement.getElementsByTagName("marker");
                var bounds = new google.maps.LatLngBounds();
                for (var i = 0; i < markerNodes.length; i++) {
                    var name = markerNodes[i].getAttribute("name");
                    var address = markerNodes[i].getAttribute("address");
                    var distance = parseFloat(markerNodes[i].getAttribute("distance"));
                    var latlng = new google.maps.LatLng(
                        parseFloat(markerNodes[i].getAttribute("lat")),
                        parseFloat(markerNodes[i].getAttribute("lng")));

                    createOption(name, distance, i);
                    createMarker(latlng, name, address);
                    bounds.extend(latlng);
                }
                map.fitBounds(bounds);
                locationSelect.style.visibility = "visible";
                locationSelect.onchange = function() {
                    var markerNum = locationSelect.options[locationSelect.selectedIndex].value;
                    google.maps.event.trigger(markers[markerNum], 'click');
                };
            });
        }

        function createMarker(latlng, name, address) {
            var html = "<b>" + name + "</b> <br/>" + address;
            var marker = new google.maps.Marker({
                map: map,
                position: latlng
            });
            google.maps.event.addListener(marker, 'click', function() {
                infoWindow.setContent(html);
                infoWindow.open(map, marker);
            });
            markers.push(marker);
        }

        function createOption(name, distance, num) {
            var option = document.createElement("option");
            option.value = num;
            option.innerHTML = name + "(" + distance.toFixed(1) + ")";
            locationSelect.appendChild(option);
        }

        function downloadUrl(url, callback) {
            var request = window.ActiveXObject ?
                new ActiveXObject('Microsoft.XMLHTTP') :
                new XMLHttpRequest;

            request.onreadystatechange = function() {
                if (request.readyState == 4) {
                    request.onreadystatechange = doNothing;
                    callback(request.responseText, request.status);
                }
            };

            request.open('GET', url, true);
            request.send(null);
        }

        function parseXml(str) {
            if (window.ActiveXObject) {
                var doc = new ActiveXObject('Microsoft.XMLDOM');
                doc.loadXML(str);
                return doc;
            } else if (window.DOMParser) {
                return (new DOMParser).parseFromString(str, 'text/xml');
            }
        }

        function doNothing() {}

        //]]>
    </script>
</head>

<body style="margin:0px; padding:0px;" onload="load()">
<div>
    <input type="text" id="addressInput" size="10"/>
    <select id="radiusSelect">
        <option value="25" selected>25mi</option>
        <option value="100">100mi</option>
        <option value="200">200mi</option>
    </select>

    <input type="button" onclick="searchLocations()" value="Search"/>
</div>
<div><select id="locationSelect" style="width:100%;visibility:hidden"></select></div>
<div id="map" style="width: 100%; height: 80%"></div>
</body>
</html>



<!--
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
        html { height: 100% }
        body { height: 100%; margin: 0px; padding: 0px }
        #map_canvas { height: 100% }
    </style>
    <script type="text/javascript"
            src="http://maps.google.com/maps/api/js?sensor=false">
    </script>
    <script type="text/javascript">
        function initialize() {
            var latlng = new google.maps.LatLng(37.869565, -122.258786);
            var myOptions = {
                zoom: 17,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            var map = new google.maps.Map(document.getElementById("map_canvas"),
                myOptions);

            // Creating a marker and positioning it on the map
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(37.869565, -122.258786),
                map: map
            });


        }

    </script>

</head>
<body >
<div id="map_canvas" style="width:100%; height:100%"></div>
</body>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBRGXPuL5hPBuvAr2BxDxenMuUDuMXAhb4 &callback=initialize"></script>
</html>
-->
<?php

/*<form name="paypalFrm" id="paypalFrm_<?php echo $subscribe[$i]->subscriptionpackageID;?>" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_ext-enter">
    <input type="hidden" name="redirect_cmd" value="_xclick"> //useful to get your custom variable back in response

    <input type="hidden" name="return" value="<?php echo base_url('clinic/paid');?>">
    //set url name where you can check for your response
    <input type="hidden" name="cancel_return" value="<?php echo base_url('clinic/cancel');?>">
    // set url to check response when user denied or cancel payment and want to back

    <input type="hidden" name="notify_url" value="<?php echo base_url('clinic/notify');?>">
    // to check please set send email logic and check for     response
    <input type="hidden" name="business" value="adc.haren@gmail.com">
    //your bussiness acoount in which payment is done
    //account in which you want to get paid (owers account)

    <input type="hidden" name="item_name" value="Purchage 10 docs">
    <input type="hidden" name="quantity_1" value="1">

    <input type="hidden" name="currency_code" value="USD">
    <input type="hidden" name="amount" value="1">   //your amount
    <input type="hidden" name="custom" value="<?php echo $userinfo[0]->userId;?>">



    <input type="hidden" name="on1" value="<?php echo $userinfo[0]->firstName;?>">
    <input type="hidden" name="on0" value="<?php echo $userinfo[0]->userId;?>">
    <input type="hidden" name="on2" value="<?php echo $subscribe[$i]->Amount;?>">
    <input type="hidden" name="on3" value="<?php echo $subscribe[$i]->packageName;?>">

</form>

<?php

public function paid()
{
    $uid = $this->session->userdata('userId');

    if (isset($_REQUEST['payment_status'])) {

        echo "<pre>";
        print_r($_REQUEST['payment_status']);
        // Your bussiness logic
    } else {
        // Your bussiness logic
    }
}
 public function cancel()
{
    // your bussiness logic if client do ot want to pay
}

          public function notify()
{

    echo "<pre>";
    print_r($_REQUEST);
    die;
}*/