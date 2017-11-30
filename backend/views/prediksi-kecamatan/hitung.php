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
    <?= "T Akhir : " . $t_akhir . "<br>" ?>
    <?php
    foreach ($solusi_min as $key => $value) {
        echo "Solusi" . $key . " : " . $value . "<br>";
    }
    
    $dataChart = '[';
    foreach ($error_min as $key => $value) {
//        echo "Error" . $key . " : " . $value . "<br>";
        $dataChart .= '{y:'.$key.', item1:'.$value.'},';
    }
    
//    echo $error_min[sizeof($error_min)];
    
    $dataChart .= ']';
    ?>
    <?php //echo "Error : " . $error_min . "<br>" ?> 

    <h3>My Google Maps Demo</h3>
    <div id="map"></div>

    <h3>Grafik Nilai Error</h3>
    <div class="box-body chart-responsive">
        <div class="chart" id="line-chart" style="height: 300px;"></div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyBCowEtFYcc6rnuHniA4_t883RgxdRBm-I"></script>
    <script type="text/javascript" src="../../web/asset/geoxml3/polys/geoxml3.js"></script>
    
    <script type="text/javascript" src="../../web/asset/plugins/jQuery/jquery-2.2.3.min.js"></script>        
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script type="text/javascript" src="../../web/asset/plugins/morris/morris.min.js"></script>        
    
    <script>
        var jember = new google.maps.LatLng(-8.17546958726021, 113.7026596069336);
        var petaoption = {zoom: 9, center: jember, mapTypeId: google.maps.MapTypeId.ROADMAP};
        peta = new google.maps.Map(document.getElementById("map"), petaoption);

        var geoXml = new geoXML3.parser({map: peta});
        var path = "../../kml/jember.kml";
        geoXml.parse(path);
        
        var js_array = <?php echo $dataChart ?>;
        console.log(js_array);
        
        var line = new Morris.Line({
            element: 'line-chart',
            resize: true,
            data: js_array,
//            data: [
//                {y: 1, item1: 2666},
//                {y: 2, item1: 2778},
//                {y: 3, item1: 4912},
//                {y: 4, item1: 3767},
//                {y: 5, item1: 6810},
//                {y: 6, item1: 5670},
//                {y: 7, item1: 4820},
//            ],
            xkey: 'y',
            ykeys: ['item1'],
            labels: ['Item 1'],
            lineColors: ['#3c8dbc'],
            hideHover: 'auto'
        });        
    </script>

</div>
