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

<!--    <table style="margin-left:12px;">
        <tr>
            <td style="padding: 3px;">
                <svg width="50px" height="20">
                <rect width="100%" height="100" style="fill:rgb(255,208,176);" /> 
                </svg>
            </td>
            <td style="margin-left:0px"><= 1.1034</td>
        </tr>
        <tr>
            <td style="padding: 3px;">
                <svg width="50px" height="20">
                <rect width="100%" height="100" style="fill:rgb(255,169,150);" /> 
                </svg>
            </td>
            <td>> 1.1034 and <= 1.4412</td>
        </tr>
        <tr>
            <td style="padding: 3px;">
                <svg width="50px" height="20">
                <rect width="100%" height="100" style="fill:rgb(230,133,124);" /> 
                </svg>
            </td>
            <td>> 1.4412 and <= 1.6276</td>
        </tr>
        <tr>
            <td style="padding: 3px;">
                <svg width="50px" height="20">
                <rect width="100%" height="100" style="fill:rgb(209,98,109);" /> 
                </svg>
            </td>
            <td>> 1.6276 and <= 2.0884</td>
        </tr>
        <tr>
            <td style="padding: 3px;">
                <svg width="50px" height="20">
                <rect width="100%" height="100" style="fill:rgb(181,75,99);" /> 
                </svg>
            </td>
            <td>> 2.0884 and <= 11.0406</td>
        </tr>
    </table>-->
    
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyBCowEtFYcc6rnuHniA4_t883RgxdRBm-I"></script>
    <script type="text/javascript" src="localhost:2525/ta_dbd/backend/web/asset/geoxml3/kmz/geoxml3.js"></script>
    <script>       
        var surabaya = new google.maps.LatLng(-8.17546958726021, 113.7026596069336);
        var petaoption = {zoom: 9, center: surabaya, mapTypeId: google.maps.MapTypeId.ROADMAP};
        peta = new google.maps.Map(document.getElementById("map"), petaoption);
        
        var geoXml = new geoXML3.parser({map: peta});
        var path = "localhost:2525/ta_dbd/backend/kml/jember.kml";
        geoXml.parse(path);
        
//        document.addEventListener("DOMContentLoaded", function(event) { 
//            event.preventDefault();
////            $('#map').html('');
////            document.getElementById('judul').innerHTML = 'Peta Crime Rate';
//            var jember = new google.maps.LatLng(-8.17546958726021, 113.7026596069336);
//            var petaoption = {zoom: 9, center: jember, mapTypeId: google.maps.MapTypeId.ROADMAP};
//            peta = new google.maps.Map(document.getElementById("map"), petaoption);
//            var geoXml = new geoXML3.parser({map: peta});
//
////            var startPath = "kml/cr_";
////            var middlePath = "_";
////            var endPath = ".kml";
//            var path = "C:/xampp/htdocs/ta_dbd/backend/kml/jember.kml";
//            geoXml.parse(path);
////            console.log(path);
////            google.maps.event.addListener(peta, 'click', function (event) {
////                kasihtanda(event.latLng);
////            });
//        });        
        
//        $(document).ready(function(){
//            event.preventDefault();
//            $('#map').html('');
////            document.getElementById('judul').innerHTML = 'Peta Crime Rate';
//            var surabaya = new google.maps.LatLng(-7.273069, 112.754513);
//            var petaoption = {zoom: 15, center: surabaya, mapTypeId: google.maps.MapTypeId.ROADMAP};
//            peta = new google.maps.Map(document.getElementById("map"), petaoption);
//            var geoXml = new geoXML3.parser({map: peta});
//
////            var startPath = "kml/cr_";
////            var middlePath = "_";
////            var endPath = ".kml";
//            var path = "localhost:2525/ta_dbd/backend/kml/surabaya_2_2016.kml";
//            geoXml.parse(path);
////            console.log(path);
////            google.maps.event.addListener(peta, 'click', function (event) {
////                kasihtanda(event.latLng);
////            });
//        });

    </script>

    <!--<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBG7PYXZXcFKSgGHW7LS6leKP25OyZE04M&callback=initMap"></script>-->

</div>
