<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\components\Helper;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PrediksiKecamatanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Prediksi Demam Berdarah';
$this->params['breadcrumbs'][] = $this->title;

$baseUrl = Yii::$app->urlManager->createAbsoluteUrl(['/']);
?>
<div class="prediksi-kecamatan-index">
    <p id="kecamatan" class="hidden"><?php echo $params['kecamatan'] ?></p>
    <p id="bilacak" class="hidden"><?php echo $params['metodebilacak'] ?></p>
    <p id="perhitungan" class="hidden"><?php echo $params['metodeperhitungan'] ?></p>

    <h1><?= Html::encode($this->title) ?></h1>
    
    <form method="GET" action="<?php echo $baseUrl.'prediksi-kecamatan/get-prediksi'; ?>">
        <div class="row">
            <div class="form-group col-md-4">
                <label>Pilih Kecamatan</label>
                <select class="form-control" name="kecamatan">
                    <option id="kecamatan" value="kecamatan">Tiap Kecamatan</option>
                    <option id="jember" value="jember">Seluruh Jember</option>
                </select>
            </div>
            
            <div class="form-group col-md-4">
                <label>Pilih Metode Perhitungan</label>
                <select class="form-control" name="metodeperhitungan">
                    <option id="linear" value="linear">Linear</option>
                    <option id="kuadratik" value="kuadratik">Kuadratik</option>
                </select>
            </div>

            <div class="form-group col-md-4">
                <label>Pilih Metode Bilangan Acak</label>
                <select class="form-control" name="metodebilacak">
                    <option id="biasa" value="biasa">Bilangan Acak Biasa</option>
                    <option id="ratanol" value="ratanol">Bilangan Acak Rata-rata 0</option>
                    <option id="distribusi" value="distribusi">Bilangan Acak Distribusi Normal</option>
                </select>
            </div>
            
<!--            <div class="form-group col-md-3">
                <label>Pilih Bulan</label>
                <select class="form-control" name="bulan">
                    <option value="01">Januari</option>
                    <option value="02">Februari</option>
                    <option value="03">Maret</option>
                </select>
            </div>-->

            <div class="form-group col-md-4">
                <input type="submit" class="btn btn-primary" value="Hitung Prediksi">
            </div>
        </div>
    </form>
    
    
    
    <!--buat hitung seluruh kecamatan/jember--> 
    <?php
    if($params['kecamatan'] == 'jember'){
    ?>
    <div class="panel panel-primary">
        <div class="panel-heading"><h4>Tabel Prediksi</h4></div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">KECAMATAN</th>
                        <th style="text-align: center;">KASUS</th>
                        <th style="text-align: center;">PREDIKSI</th>
                        <th style="text-align: center;">ERROR</th>
                    </thead>
                    <tbody>
                        <?php
                        for ($i=0; $i < sizeof($params['table']); $i++) {
                        echo "<tr>";
                        echo "<td style='text-align: center;'>" . ($i+1) . "</td>";
                        echo "<td style='text-align: center;'>" . $params['table'][$i]['kecamatan'] . "</td>";
                        echo "<td style='text-align: center;'>" . $params['table'][$i]['kasus'] . "</td>";
                        echo "<td style='text-align: center;'>" . $params['table'][$i]['prediksi'] . "</td>";
                        echo "<td style='text-align: center;'>" . $params['table'][$i]['error'] . "</td>";
                        echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
    }
    ?>
    
    <!--buat hitung kecamatan-->
    <?php
    if($params['kecamatan'] != 'jember'){
    ?>
    <div class="panel panel-primary">
        <div class="panel-heading"><h4>Tabel Prediksi</h4></div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">KECAMATAN</th>
                        <th style="text-align: center;">KASUS</th>
                        <th style="text-align: center;">PREDIKSI</th>
                        <th style="text-align: center;">ERROR</th>
                    </thead>
                    <tbody>
                        <?php
                        for ($i=0; $i < sizeof($params)-5; $i++) {
                        echo "<tr>";
                        echo "<td style='text-align: center;'>" . ($i+1) . "</td>";
                        echo "<td style='text-align: center;'>" . $params[$i]['table']['kecamatan'] . "</td>";
                        echo "<td style='text-align: center;'>" . $params[$i]['table']['kasus'] . "</td>";
                        echo "<td style='text-align: center;'>" . $params[$i]['table']['prediksi'] . "</td>";
                        echo "<td style='text-align: center;'>" . $params[$i]['table']['error'] . "</td>";
                        echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
    }
    ?>
    

    
    <?php
//    $dataChart = '[';
//    foreach ($params['table'] as $key => $value) {
//        $dataChart .= '{y:'.$key.', item1:'.$value.'},';
//    }
//    $dataChart .= ']';
    ?>

    
    <h3>Peta Persebaran Penyakit Demam Berdarah</h3>
    <div id="map2"></div>
    
    <h3>Peta Prediksi Persebaran Penyakit Demam Berdarah</h3>
    <div id="map"></div>
    
    
    <div style="margin-top: 20px;">
        <p><i>*Keterangan</i></p>
        <span class="label label-success">Aman</span>
        <span class="label label-warning">Siaga</span>
        <span class="label label-danger">Bahaya</span>
    </div>


<!--    <h3>Grafik Nilai Error</h3>
    <div class="box-body chart-responsive">
        <div class="chart" id="line-chart" style="height: 300px;"></div>
    </div>-->
        
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyBCowEtFYcc6rnuHniA4_t883RgxdRBm-I"></script>
    <script type="text/javascript" src="../../web/asset/geoxml3/polys/geoxml3.js"></script>
    
    <script type="text/javascript" src="../../web/asset/plugins/jQuery/jquery-2.2.3.min.js"></script>        
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script type="text/javascript" src="../../web/asset/plugins/morris/morris.min.js"></script>        
    
    
    <script>
        var map, map2;
        initialize();
        function initialize() {
            // create the maps
            var myOptions = {
                zoom: 9,
                center: new google.maps.LatLng(-8.17546958726021, 113.7026596069336),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }
            
            peta = new google.maps.Map(document.getElementById("map"), myOptions);
            var geoXml = new geoXML3.parser({map: peta});
            var path = "../../kml/test.kml";
            geoXml.parse(path);
            
            peta2 = new google.maps.Map(document.getElementById("map2"), myOptions);
            var geoXml2 = new geoXML3.parser({map: peta2});
            var path2 = "../../kml/kecamatan.kml";
            geoXml2.parse(path2);
        }

        
        
        var kecamatan = '#' + $('#kecamatan').text();
        $(kecamatan).attr('selected','selected');
        
        var metodebilacak = '#' + $('#bilacak').text();
        $(metodebilacak).attr('selected','selected');
        
        var metodeperhitungan = '#' + $('#perhitungan').text();
        $(metodeperhitungan).attr('selected','selected');
            

        
        //var js_array = <?php // echo $dataChart ?>;
        //console.log(js_array);
        
        //var line = new Morris.Line({
            //element: 'line-chart',
            //resize: true,
            //data: js_array,
//            data: [
//                {y: 1, item1: 2666},
//                {y: 2, item1: 2778},
//                {y: 3, item1: 4912},
//                {y: 4, item1: 3767},
//                {y: 5, item1: 6810},
//                {y: 6, item1: 5670},
//                {y: 7, item1: 4820},
//            ],
            //xkey: 'y',
            //ykeys: ['item1'],
            //labels: ['Item 1'],
            //lineColors: ['#3c8dbc'],
            //hideHover: 'auto'
        //});        
    </script>

</div>
