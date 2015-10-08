<?php

/**
 * CakeMapHelper
 * Helper to CakePHP framework that integrates a Google Map in your view
 * using Google Maps API V3.
 * This helper uses the latest Google API V3 so you don't need to provide or get any Google API Key
 *
 * @author      Marc Fernandez Girones <marc.fernandezg@gmail.com>
 * @author      George Mponos <gmponos@gmail.com>
 * @version     3.0
 */

namespace CakeMap\View\Helper;

use Cake\View\Helper;

class CakeMapHelper extends Helper
{
    /**
     * @var array
     */
    public $options = [
        'id' => 'map_canvas',
        'width' => '600px',
        'height' => '600px',
        'style' => 'style',
        'zoom' => 13,
        'type' => 'ROADMAP',
        'custom' => null,
        'latitude' => 40.69847032728747,
        'longitude' => -73.9514422416687,
        'localize' => true,
        'marker' => true,
        'markerTitle' => 'My Position',
        'markerIcon' => 'http://google-maps-icons.googlecode.com/files/home.png',
        'markerShadow' => '',
        'infoWindow' => true,
        'windowText' => 'My Position',
        'infoWindowM' => 'true',
        'windowTextM' => 'Marker info window',
        'markerTitleM' => 'Title',
        'markerIconM' => "http://maps.google.com/mapfiles/marker.png",
        'markerShadowM' => "http://maps.google.com/mapfiles/shadow50.png",
        'travelMode' => "driving",
        'directionsDiv' => "null",
        'strokeColor' => '#FF0000',
        'strokeOpacity' => 1.0,
        'strokeWeight' => 2.0,
        'fillColor' => "",
        'fillOpacity' => 0,
        'draggableMarker' => false,
    ];

    /**
     * @var string
     */
    private static $version = '3.0.0';

    /**
     * Get the version of this helper
     *
     * @return string
     */
    public static function getVersion()
    {
        return self::$version;
    }

    /**
     * This method generates a div tag and inserts a google maps.
     *
     * @author Marc Fernandez <marc.fernandezg (at) gmail (dot) com>
     * @param array $options options array
     * @return string Return all the javascript script to generate the map
     */
    public function map(array $options = [])
    {
        $id = $width = $height = $style = $zoom = $type = $custom = $longitude = $latitude = $location =
        $localize = $marker = $markerTitle = $markerIcon = $markerShadow = $infoWindow = $windowText =
        $fillColor = $fillOpacity = $draggableMarker = null;

        $options = array_merge($this->options, $options);
        if ($options != null) {
            extract($options);
        }

        $map = "<div id='$id' style='width:$width; height:$height; $style'></div>";
        $map .= "
      <script>
        var markers = new Array();
        var markersIds = new Array();
        var geocoder = new google.maps.Geocoder();

        function geocodeAddress(address, action, map,markerId, markerTitle, markerIcon, markerShadow, windowText, showInfoWindow, draggableMarker) {
            geocoder.geocode( { 'address': address}, function(results, status) {
              if (status == google.maps.GeocoderStatus.OK) {
                if(action =='setCenter'){
                  setCenterMap(results[0].geometry.location);
                }
                if(action =='setMarker'){
                  //return results[0].geometry.location;
                  setMarker(map,markerId,results[0].geometry.location,markerTitle, markerIcon, markerShadow,windowText, showInfoWindow, draggableMarker);
                }
                if(action =='addPolyline'){
                  return results[0].geometry.location;
                }
              } else {
                alert('Geocode was not successful for the following reason: ' + status);
                return null;
              }
            });
        }";

        $map .= "
      var initialLocation;
        var browserSupportFlag =  new Boolean();
        var {$id};
        var myOptions = {
          zoom: {$zoom},
          mapTypeId: google.maps.MapTypeId.{$type}
          " . (($custom != "") ? ",$custom" : "") . "

        };
        {$id} = new google.maps.Map(document.getElementById('$id'), myOptions);
    ";
        $map .= "
      function setCenterMap(position){
    ";
        if ($localize) {
            $map .= "localize();";
        } else {
            $map .= "{$id}.setCenter(position);";
            if (!preg_match('/^https?:\/\//', $markerIcon)) {
                $markerIcon = $this->webroot . IMAGES_URL . '/' . $markerIcon;
            }
            if ($marker) {
                $map .= "setMarker({$id},'center',position,'{$markerTitle}','{$markerIcon}','{$markerShadow}','{$windowText}', " . ($infoWindow ? 'true' : 'false') . "," . ($draggableMarker ? 'true' : 'false') . ");";
            }
        }
        $map .= "
      }
    ";
        if (!empty($latitude) && !empty($longitude)) {
            $map .= "setCenterMap(new google.maps.LatLng({$latitude}, {$longitude}));";
        } else {
            if (isset($address)) {
                $map .= "var centerLocation = geocodeAddress('{$address}','setCenter'); setCenterMap(centerLocation);";
            } else {
                $map .= "setCenterMap(new google.maps.LatLng({$this->defaultLatitude}, {$this->defaultLongitude}));";
            }
        }
        $map .= "
      function localize(){
            if(navigator.geolocation) { // Try W3C Geolocation method (Preferred)
                browserSupportFlag = true;
                navigator.geolocation.getCurrentPosition(function(position) {
                  initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
                  {$id}.setCenter(initialLocation);";
        if (!preg_match('/^https?:\/\//', $markerIcon)) {
            $markerIcon = $this->webroot . IMAGES_URL . '/' . $markerIcon;
        }
        if ($marker) {
            $map .= "setMarker({$id},'center',initialLocation,'{$markerTitle}','{$markerIcon}','{$markerShadow}','{$windowText}', " . ($infoWindow ? 'true' : 'false') . "," . ($draggableMarker ? 'true' : 'false') . ");";
        }

        $map .= "}, function() {
                  handleNoGeolocation(browserSupportFlag);
                });

            } else if (google.gears) { // Try Google Gears Geolocation
          browserSupportFlag = true;
          var geo = google.gears.factory.create('beta.geolocation');
          geo.getCurrentPosition(function(position) {
            initialLocation = new google.maps.LatLng(position.latitude,position.longitude);
            {$id}.setCenter(initialLocation);";
        if ($marker) {
            $map .= "setMarker({$id},'center',initialLocation,'{$markerTitle}','{$markerIcon}','{$markerShadow}','{$windowText}', " . ($infoWindow ? 'true' : 'false') . "," . ($draggableMarker ? 'true' : 'false') . ");";
        }

        $map .= "}, function() {
                  handleNoGeolocation(browserSupportFlag);
                });
            } else {
                // Browser doesn't support Geolocation
                browserSupportFlag = false;
                handleNoGeolocation(browserSupportFlag);
            }
        }

        function handleNoGeolocation(errorFlag) {
            if (errorFlag == true) {
              initialLocation = noLocation;
              contentString = \"Error: The Geolocation service failed.\";
            } else {
              initialLocation = noLocation;
              contentString = \"Error: Your browser doesn't support geolocation.\";
            }
            {$id}.setCenter(initialLocation);
            {$id}.setZoom(3);
        }";

        $map .= "
      function setMarker(map, id, position, title, icon, shadow, content, showInfoWindow, draggableMarker){
        var index = markers.length;
        markersIds[markersIds.length] = id;
        markers[index] = new google.maps.Marker({
                position: position,
                map: map,
                icon: icon,
                shadow: shadow,
                draggable: draggableMarker,
                title:title
            });
           if(content != '' && showInfoWindow){
             var infowindow = new google.maps.InfoWindow({
                  content: content
              });
             google.maps.event.addListener(markers[index], 'click', function() {
            infowindow.open(map,markers[index]);
              });
            }
            if (draggableMarker) {
              google.maps.event.addListener(markers[index], 'dragend', function(event) {
                updateCoordinatesDisplayed(id, event.latLng.lat(), event.latLng.lng());
              });
            }
         }";
        $map .= "
          // An input with an id of 'latitude_<id>' and 'longitude_<id>' will be set, only if it exist
          function updateCoordinatesDisplayed(markerId, latitude, longitude) {
            if (document.getElementById('latitude_' + markerId)) {
              document.getElementById('latitude_' + markerId).value = latitude;
            }
            if (document.getElementById('longitude_' + markerId)) {
              document.getElementById('longitude_' + markerId).value = longitude;
            }
          }
         ";
        $map .= "
          // remove a marker from map
      function removeMarker(id){
       var index = markersIds.indexOf(String(id));
       if (index > -1) {
           markers[index].setMap(null);
           return true;
       }
       return false;
        }
        // add a marker back to map
        function addMarker(id, map){
       var index = markersIds.indexOf(String(id));
       if (index > -1) {
           markers[index].setMap(map);
           return true;
       }
       return false;
        }";

        $map .= "</script>";
        return $map;
    }


    /**
     * Method addMarker
     * This method puts a marker in the google map generated with the method map
     *
     * @author Marc Fernandez <marc.fernandezg (at) gmail (dot) com>
     * @param string $mapId    Id that you used to create the map (default 'map_canvas')
     * @param string $id       Unique identifier for the marker
     * @param mixed  $position string with the address or an array with latitude and longitude
     * @param array  $options  options array
     * @return string Return all the javascript script to add the marker to the map
     */
    public function addMarker($mapId, $id, $position, array $options = [])
    {
        $longitude = $latitude =
        $markerTitle = $markerIcon = $markerShadow = $infoWindow = $windowText =
        $fillColor = $fillOpacity = $draggableMarker = null;

        if ($id == null || $mapId == null || $position == null) {
            return null;
        }
        $geolocation = false;
        // Check if position is array and has the two necessary elements
        // or if is not array that the string is not empty
        //todo add address to
        if (is_array($position)) {
            if (empty($position["latitude"]) || empty($position["longitude"])) {
                return null;
            }
            $latitude = $position["latitude"];
            $longitude = $position["longitude"];
        } else {
            $geolocation = true;
        }

        $options = array_merge($this->options, $options);
        extract($options);

        $markerTitle = addslashes($markerTitle);
        $windowText = addslashes($windowText);

        $marker = "<script>";

        if (!$geolocation) {
            if (!preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/", $latitude) || !preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/",
                    $longitude)
            ) {
                return null;
            }
            if (!preg_match('/^https?:\/\//', $markerIcon)) {
                $markerIcon = $this->webroot . IMAGES_URL . '/' . $markerIcon;
            }
            $marker .= "setMarker({$mapId},'{$id}',new google.maps.LatLng($latitude, $longitude),'{$markerTitle}','{$markerIcon}','{$markerShadow}','{$windowText}', " . ($infoWindow ? 'true' : 'false') . "," . ($draggableMarker ? 'true' : 'false') . ")";
        } else {
            if (empty($position)) {
                return null;
            }
            if (!preg_match('/^https?:\/\//', $markerIcon)) {
                $markerIcon = $this->webroot . IMAGES_URL . '/' . $markerIcon;
            }
            $marker .= "geocodeAddress('{$position}', 'setMarker', {$mapId},'{$id}','{$markerTitle}','{$markerIcon}','{$markerShadow}','{$windowText}', " . ($infoWindow ? 'true' : 'false') . "," . ($draggableMarker ? 'true' : 'false') . ")";
        }

        $marker .= "</script>\n";
        return $marker;
    }

    /**
     * Method clusterMarkers
     * This method collects all markers into clusters and utilizes the MarkerCluster utility
     *
     * @author Corie Slate <corie.slate (at) gmail (dot) com>
     * @param string $map_id Id that you used to create the map (default 'map_canvas')
     * @return string will return all the javascript script to add the clusterer to the map
     */
    public function clusterMarkers($map_id)
    {
        if ($map_id == null) {
            return null;
        }

        $cluster = "<script>";

        $cluster .= "var markerCluster = new MarkerClusterer({$map_id}, markers);";

        $cluster .= "</script>";
        return $cluster;
    }

    /**
     * Method getDirections
     * This method gets the direction between two addresses or markers
     *
     * @author Marc Fernandez <marc.fernandezg (at) gmail (dot) com>
     * @param string $mapId    Id that you used to create the map (default 'map_canvas')
     * @param string $id       Unique identifier for the directions
     * @param array  $position array with strings with the from and to addresses or from and to markers
     * @param array  $options  options array
     * @return string Return all the javascript script to add the directions to the map
     */
    public function getDirections($mapId, $id, array $position, array $options = [])
    {
        if ($id == null || $mapId == null || $position == null) {
            return null;
        }

        if (!isset($position["from"]) || !isset($position["to"])) {
            return null;
        }

        $id = $width = $height = $style = $zoom = $type = $custom = $longitude = $latitude = $location =
        $localize = $marker = $markerTitle = $markerIcon = $markerShadow = $infoWindow = $windowText =
        $directionsDiv = $travelMode =
        $fillColor = $fillOpacity = $draggableMarker = $strokeColor = $strokeOpacity = $strokeWeight = null;

        $options = array_merge($this->options, $options);
        if ($options != null) {
            extract($options);
        }

        if (is_array($position["from"])) {
            $position["from"] = "new google.maps.LatLng({$position["from"]["latitude"]}, {$position["from"]["longitude"]})";
        } else {
            $position["from"] = "'{$position["from"]}'";
        }

        if (is_array($position["to"])) {
            $position["to"] = "new google.maps.LatLng({$position["to"]["latitude"]}, {$position["to"]["longitude"]})";
        } else {
            $position["to"] = "'{$position["to"]}'";
        }

        $directions = "
      <script>
        var {$id}Service = new google.maps.DirectionsService();
        var {$id}Display;
        {$id}Display = new google.maps.DirectionsRenderer();
        {$id}Display.setMap({$mapId});
      ";
        if ($directionsDiv != null) {
            $directions .= "{$id}Display.setPanel(document.getElementById('{$directionsDiv}'));";
        }

        $directions .= "
        var request = {
          origin:{$position["from"]},
          destination:{$position["to"]},
          travelMode: google.maps.TravelMode.{$travelMode}
        };
        {$id}Service.route(request, function(result, status) {
          if (status == google.maps.DirectionsStatus.OK) {
            {$id}Display.setDirections(result);
          }
        });
      </script>
    ";
        return $directions;
    }

    /**
     * Method addPolyline
     * This method adds a line between 2 points
     *
     * @author Marc Fernandez <marc.fernandezg (at) gmail (dot) com>
     * @param string $mapId    Id that you used to create the map (default 'map_canvas')
     * @param string $id       Unique identifier for the directions
     * @param array  $position array with start and end latitudes and longitudes
     * @param array  $options  options array
     * @return string Return all the javascript script to add the directions to the map
     */
    public function addPolyline($mapId, $id, array $position, array $options = [])
    {
        if ($id == null || $mapId == null || $position == null) {
            return null;
        }

        if (!isset($position["start"]) || !isset($position["end"])) {
            return null;
        }

        $strokeColor = $strokeOpacity = $strokeWeight = null;
        $options = array_merge($this->options, $options);
        if ($options != null) {
            extract($options);
        }

        // Check if position is array and has the two necessary elements
        if (is_array($position["start"])) {
            if (!isset($position["start"]["latitude"]) || !isset($position["start"]["longitude"])) {
                return null;
            }
            $latitude_start = $position["start"]["latitude"];
            $longitude_start = $position["start"]["longitude"];
        }

        if (is_array($position["end"])) {
            if (!isset($position["end"]["latitude"]) || !isset($position["end"]["longitude"])) {
                return null;
            }
            $latitude_end = $position["end"]["latitude"];
            $longitude_end = $position["end"]["longitude"];
        }

        $polyline = "<script>";

        if (!preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/", $latitude_start) || !preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/",
                $longitude_start)
        ) {
            return null;
        }
        $polyline .= "var start = new google.maps.LatLng({$latitude_start}, {$longitude_start}); ";

        if (!preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/", $latitude_end) || !preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/",
                $longitude_end)
        ) {
            return null;
        }

        $polyline .= "var end = new google.maps.LatLng({$latitude_end}, {$longitude_end}); ";

        $polyline .= "
        var poly = [
          start,
          end
        ];
        var {$id}Polyline = new google.maps.Polyline({
          path: poly,
          strokeColor: '{$strokeColor}',
          strokeOpacity: {$strokeOpacity},
          strokeWeight: {$strokeWeight}
        });
        {$id}Polyline.setMap({$mapId});

      </script>
      ";
        return $polyline;
    }

    /**
     * Method addCircle
     * This method adds a circle around a center point
     *
     * @author Marc Fernandez <marc.fernandezg (at) gmail (dot) com>
     * @param string $mapId   Id that you used to create the map (default 'map_canvas')
     * @param string $id      Unique identifier for the directions
     * @param string $center
     * @param int    $radius
     * @param array  $options array with extra options
     * @return string Return all the javascript script to add the directions to the map
     */
    public function addCircle($mapId, $id, $center, $radius = 100, array $options = [])
    {
        if ($id == null || $mapId == null || $center == null) {
            return null;
        }

        $fillColor = $fillOpacity = $draggableMarker = $strokeColor = $strokeOpacity = $strokeWeight = null;
        $options = array_merge($this->options, $options);
        if ($options != null) {
            extract($options);
        }

        // Check if position is array and has the two necessary elements
        if (is_array($center)) {
            if (!isset($center["latitude"]) || !isset($center["longitude"])) {
                return null;
            }
            $latitude_center = $center["latitude"];
            $longitude_center = $center["longitude"];
        } else {
            return "Error: Center needs latitude and longiture";
        }

        $circle = "<script>";


        if (!preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/", $latitude_center) || !preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/",
                $longitude_center)
        ) {
            return null;
        }
        $circle .= "var center = new google.maps.LatLng({$latitude_center}, {$longitude_center}); ";

        $circle .= "
        var {$id}Circle = new google.maps.Circle({
          strokeColor: '{$strokeColor}',
          strokeOpacity: {$strokeOpacity},
          strokeWeight: {$strokeWeight},
          fillColor: '{$fillColor}',
          fillOpacity: {$fillOpacity},
          center: center,
          radius: {$radius}
        });
        {$id}Circle.setMap({$mapId});

      </script>
      ";
        return $circle;
    }
}
