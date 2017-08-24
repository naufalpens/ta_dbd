<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PrediksiKecamatanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Prediksi Kecamatans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prediksi-kecamatan-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Prediksi Kecamatan', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Hitung Linear', ['hitung-linear'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Hitung Kuadratik', ['hitung-kuadratik'], ['class' => 'btn btn-warning']) ?>
    </p>

    <?= "Kasus : " . $kasus . "<br>" ?>
    <?= "Prediksi : " . $prediksi_min . "<br>" ?>
    <?php
    foreach ($solusi_min as $key => $value) {
        echo "Solusi" . $key . " : " . $value . "<br>";
    }
    ?>
    <?= "Error : " . $error_min . "<br>" ?> 

    <h3>My Google Maps Demo</h3>


    <div id="map"></div>

    <script>
        var map;
        var src = 'http://naufalpens.it.student.pens.ac.id/kml/jember.kml';
        // var src = '127.0.0.1:2525/ta_dbd/backend/kml/jember.kml';
        // var src = 'https://developers.google.com/maps/documentation/javascript/examples/kml/westcampus.kml';

        function initMap() {
            var centerLatLng = {lat: -8.17546958726021, lng: 113.7026596069336};
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 9,
                center: centerLatLng
            });

            var kmlLayer = new google.maps.KmlLayer(src, {            
                suppressInfoWindows: true,
                preserveViewport: false,
                map: map
            });

//        var marker = new google.maps.Marker({
//          position: uluru,
//          map: map
//        });

//        google.maps.event.addListener(map,'click',function(e){            
//            console.log("lat : "+e.latLng.lat()+" = lng : "+e.latLng.lng());
//        });        

        }
    </script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBG7PYXZXcFKSgGHW7LS6leKP25OyZE04M&callback=initMap"></script>

</div>
