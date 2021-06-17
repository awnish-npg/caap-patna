<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(false);
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap">

    </script>
<style>
    #map {
        height: 90%;
    }

    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
    .hide-menu{
      display:none;
    }
    #map img[src*='kml'] {
        opacity: 0.7
    }
    .firstHeading{
      color: #0655a3;
      font-weight: 600;
      font-size: 14px;
    }
    .firstHeading span{
      color: #000;
      font-weight: 300;
    }
    #content{
      width:400px;
    }
</style>
<script>
    function initMap() {
        var markerPos = { lat: <?php echo $lat;?>, lng: <?php echo $lang;?> };
        var mapCenter = { lat: 29.8445, lng: 79.6039 };

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: markerPos
        });

        const contentString =
          '<div id="content">' +
          '<div id="siteNotice">' +
          "</div>" +
          '<h2 id="firstHeading" class="firstHeading"><?php echo $ticketDetails->name;?><span> near </span><?php echo $ticketDetails->landmark;?></h2>' +
          '<div id="bodyContent">' +
         "<div class='dashboard-cell w30P dashboard-comments'><?php echo trim($ticketDetails->description);?></div>" +
          "</div>" +
          "</div>";

        const infowindow = new google.maps.InfoWindow({
          content: contentString
        });
        var marker = new google.maps.Marker({
            position: markerPos,
            map: map,
            animation: google.maps.Animation.DROP,
        });
        infowindow.open(map, marker);

        marker.addListener("click", () => {
          infowindow.open(map, marker);
        });
        var kml1 = new google.maps.KmlLayer({
            url: '<?php echo $kmz_file;?>',
            map: map,
            preserveViewport: true,
        });

        // var kml2 = new google.maps.KmlLayer({
        //     url: 'https://developers.google.com/maps/documentation/javascript/examples/kml/westcampus.kml',
        //     map: map,
        //     preserveViewport: true
        // });

    }

</script>

<div id="map"></div>