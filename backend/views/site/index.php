<?php
/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-md-12">
                <h3>Total Kasus Demam Berdarah Tahun 2009-2012</h3>
            </div>
            
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="ion ion-person"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Jumlah Kasus</span>
                        <span class="info-box-text">Tahun 2009</span>
                        <span class="info-box-number">1093<small> Kasus</small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="ion ion-person"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Jumlah Kasus</span>
                        <span class="info-box-text">Tahun 2010</span>
                        <span class="info-box-number">1494<small> Kasus</small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="ion ion-person"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Jumlah Kasus</span>
                        <span class="info-box-text">Tahun 2011</span>
                        <span class="info-box-number">77<small> Kasus</small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="ion ion-person"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Jumlah Kasus</span>
                        <span class="info-box-text">Tahun 2012</span>
                        <span class="info-box-number">260<small> Kasus</small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3>Peta Persebaran Penyakit Demam Berdarah 2009-2012</h3>
                <div id="map"></div>
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <p><i>*Keterangan</i></p>
            <span class="label label-success">Aman</span>
            <span class="label label-warning">Siaga</span>
            <span class="label label-danger">Bahaya</span>
        </div>

    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyBCowEtFYcc6rnuHniA4_t883RgxdRBm-I"></script>
<script type="text/javascript" src="../../backend/web/asset/geoxml3/polys/geoxml3.js"></script>

<script>
    
    
        // create the maps
        var myOptions = {
            zoom: 9,
            center: new google.maps.LatLng(-8.17546958726021, 113.7026596069336),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        peta = new google.maps.Map(document.getElementById("map"), myOptions);
            var geoXml = new geoXML3.parser({map: peta});
            var path = "../../backend/kml/dashboard.kml";
            geoXml.parse(path);
    
</script>