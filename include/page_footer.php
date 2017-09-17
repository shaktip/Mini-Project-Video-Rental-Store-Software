


                 <!-- /. ROW  -->           
			    </div>
             <!-- /. PAGE INNER  -->
            </div>
         <!-- /. PAGE WRAPPER  -->


                        <div id="footer" >
                            <table width="98%" ><tr>
                                    <td >&nbsp;&nbsp;
                                        <a data-toggle="modal" href="#show_locations">
                                            <span style="color:#ac2925; font-size: large; font-weight: bolder"> View Store Locations </span>
                                        </a>
                                    </td>
                                    <td >&nbsp;&nbsp;
                                    <?php
                                        if($_SESSION['role'] == 'M' || $_SESSION['role'] == 'C'){
                                            echo "<b>Store Address: </b>" . $_SESSION['store_address'] . " (# " . $_SESSION['store_contact_number'] . ")";

                                        }
                                    ?>
                                    </td>
                                    <td align="right"><b>
                                            <span style="margin-right: 200px">
                                         <?php
                                         switch ($_SESSION['role']){
                                             case 'C':
                                                 echo 'Role: Staff';
                                                 break;
                                             case 'M':
                                                 echo 'Role: Manager';
                                                 break;
                                             case 'S':
                                                 echo 'Super Admin';
                                                 break;
                                             case 'member':
                                                 eligible_for_more_items($conn, $_SESSION["logged_in_id"]);
                                                 echo 'Currently issued CD/DVDs - Video: ' .
                                                     ($_SESSION["eligible_for"][0][0]-$_SESSION["eligible_for"][0][1]) .
                                                     ', Music: ' . ($_SESSION["eligible_for"][1][0]-$_SESSION["eligible_for"][1][1]);
                                                 break;
                                         }


                                         ?>
                                                </span>
                                        </b>
                                    </td>
                            </tr></table>
                        </div>
     <!-- /. WRAPPER  -->




                 <div class="modal fade" id="show_locations" role="dialog" >
                     <div class="modal-dialog modal-lg">
                         <div class="modal-content" id="back" >
                             <div class="modal-header">
                                 <table width="100%">
                                     <tr>
                                         <td><span style="font-size: x-large">VRS Store Locations</span></td>
                                         <td align="right"><a class="btn btn-default" data-dismiss="modal">Close</a></td>
                                     </tr>
                                 </table>


                             </div>
                             <div class="modal-body">
                                 <div id="map"></div>
                             </div>

                         </div>
                     </div>

                 </div>

                 <?php

                 $footer_stores = get_rows($conn,"store","*", " status = 'A' ");
                 if($footer_stores->num_rows > 0) {
                     $lat = array();
                     $lng = array();
                     $footer_store_info = array();
                     while ($footer_store_row = $footer_stores->fetch_array()) {
                         $lat[] = $footer_store_row['latitude'];
                         $lng[] = $footer_store_row['longitude'];
                         $footer_store_info[] = $footer_store_row['address'] . ", " .
                             $footer_store_row['city'] . ", Ph-" . $footer_store_row['contact_number'];
                     }

                     ?>


                     <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBRGXPuL5hPBuvAr2BxDxenMuUDuMXAhb4&callback=myMap"></script>
                     <script>
                         function initialize() {
                             var mapProp =
                                 {
                                     center: new google.maps.LatLng(42, 45),
                                     zoom: 4,
                                 };
                             var map = new google.maps.Map(document.getElementById("map"),
                                 mapProp);

                             var latitudes = <?php echo json_encode($lat); ?>;
                             var longitudes = <?php echo json_encode($lng); ?>;
                             var store_info = <?php echo json_encode($footer_store_info); ?>;

                             for (i = 0; i < latitudes.length; i++) {
//                alert(typeof(latitudes[i]) + "  " + longitudes[i] + "  " + store_info[i]);
                                 var myLatLng = {
                                     lat: parseFloat(latitudes[i]), lng: parseFloat
                                     (longitudes[i])
                                 };

                                 var marker = new google.maps.Marker({
                                     position: myLatLng,
                                     map: map,
                                     title: store_info[i]
                                 });
                                 marker.setMap(map);
                             }
                         }

                         google.maps.event.addDomListener(window, 'load', initialize);

                     </script>


                     <style>
                         #map {
                             width: 800px;
                             height: 500px;
                         }
                     </style>




                     <?php
                 }
                 ?>


                 <script>
                     $('#show_locations').on('shown.bs.modal', function () {
                         google.maps.event.trigger(map, "resize");
                     });
                 </script>



                 </body>
</html>
