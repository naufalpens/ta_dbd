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
    
    <form method="GET" action="get-prediksi">
        <div class="row col-md-12">

            <div class="form-group col-md-4">
                <label>Pilih Jenis Perhitungan</label>
                <select class="form-control" name="jenisperhitungan">
                    <option value="kecamatan">Tiap Kecamatan</option>
                    <option value="jember">Seluruh Jember</option>
                </select>
            </div>
            
            <div class="form-group col-md-4">
                <label>Pilih Metode Perhitungan</label>
                <select class="form-control" name="metodeperhitungan">
                    <option value="linear">Linear</option>
                    <option value="kuadratik">Kuadratik</option>
                </select>
            </div>

            <div class="form-group col-md-4">
                <label>Pilih Metode Bilangan Acak</label>
                <select class="form-control" name="metodebilacak">
                    <option value="biasa">Bilangan Acak Biasa</option>
                    <option value="ratanol">Bilangan Acak Rata-rata 0</option>
                    <option value="distribusi">Bilangan Acak Distribusi Normal</option>
                </select>
            </div>

            <div class="form-group col-md-6">
                <input type="submit" class="btn btn-primary" value="Hitung Prediksi">
            </div>
        </div>
    </form>

    <p>
        <?= Html::a('Create Prediksi Kecamatan', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Hitung Linear', ['hitung-linear'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Hitung Kuadratik', ['hitung-kuadratik'], ['class' => 'btn btn-warning']) ?>
    </p>

    
    <?= "Kasus : " . $params['kasus'] . "<br>" ?>
    <?= "Prediksi : " . $params['prediksi_min'] . "<br>" ?>
    <?= "Error : " . number_format(abs($params['kasus'] - $params['prediksi_min']), 2) . "<br>" ?>
    <?= "T Akhir : " . $params['t_akhir'] . "<br>" ?>
    
    <hr>
    
    <?php
    foreach ($params['solusi_min'] as $key => $value) {
        echo "Solusi" . $key . " : " . $value . "<br>";
    }
    ?>

    <div class="col-md-12" style="margin-top: 30px;">
        <table class="table table-bordered">
            <thead>
                <th style="text-align: center;">CH</th>
                <th style="text-align: center;">HH</th>
                <th style="text-align: center;">ABJ</th>
                <th style="text-align: center;">HI</th>
                <th style="text-align: center;">KASUS</th>
                <th style="text-align: center;">PREDIKSI</th>
                <th style="text-align: center;">ERROR</th>
            </thead>
                <?php
                    // var_dump($params['table']); die();
                    
                    for ($i=0; $i < sizeof($params['table']['ch']); $i++) {
                ?>
                        <tr>
                <?php
                        echo "<td style='text-align: center;'>" . $params['table']['ch'][$i] . "</td>";
                        echo "<td style='text-align: center;'>" . $params['table']['hh'][$i] . "</td>";
                        echo "<td style='text-align: center;'>" . $params['table']['abj'][$i] . "</td>";
                        echo "<td style='text-align: center;'>" . $params['table']['hi'][$i] . "</td>";
                        echo "<td style='text-align: center;'>" . $params['table']['kasus_tiap_data'][$i] . "</td>";
                        echo "<td style='text-align: center;'>" . $params['table']['prediksi_tiap_data'][$i] . "</td>";
                        echo "<td style='text-align: center;'>" . $params['table']['error_tiap_data'][$i] . "</td>";
                ?>
                        </tr>
                <?php
                    }
                ?>
            <tbody>

            </tbody>
        </table>
    </div>
    

    
    <?php
//    var_dump($params['error_min']); die();
    
    $dataChart = '[';
    foreach ($params['error_min'] as $key => $value) {
        $dataChart .= '{y:'.$key.', item1:'.$value.'},';
    }
    
    
    $dataChart .= ']';
    ?>

    
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
        var path = "../../kml/test.kml";
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
