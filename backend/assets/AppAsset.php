<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'asset/bootstrap/css/bootstrap.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css',
        'asset/dist/css/AdminLTE.min.css',
        'asset/dist/css/skins/_all-skins.min.css',
        'asset/plugins/iCheck/flat/blue.css',
        'asset/plugins/morris/morris.css',        
        'asset/plugins/jvectormap/jquery-jvectormap-1.2.2.css',
        'asset/plugins/datepicker/datepicker3.css',
        'asset/plugins/daterangepicker/daterangepicker.css',
        'asset/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css',
        'asset/plugins/datatables/dataTables.bootstrap.css',
        'asset/plugins/datatables/dataTables.bootstrap.css',
    ];
    public $js = [
        'asset/plugins/jQuery/jquery-2.2.3.min.js',
        'https://code.jquery.com/ui/1.11.4/jquery-ui.min.js',
        'asset/bootstrap/js/bootstrap.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js',
        'asset/plugins/morris/morris.min.js',
        'asset/plugins/sparkline/jquery.sparkline.min.js',
        'asset/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js',
        'asset/plugins/jvectormap/jquery-jvectormap-world-mill-en.js',
        'asset/plugins/knob/jquery.knob.js',
        'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js',
        'asset/plugins/daterangepicker/daterangepicker.js',
        'asset/plugins/datepicker/bootstrap-datepicker.js',
        'asset/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js',
        'asset/plugins/slimScroll/jquery.slimscroll.min.js',
        'asset/plugins/fastclick/fastclick.js',
        'asset/dist/js/app.min.js',
        'asset/dist/js/pages/dashboard.js',
        'asset/dist/js/demo.js',
        'asset/plugins/datatables/jquery.dataTables.min.js',
        'asset/plugins/datatables/dataTables.bootstrap.min.js',
       // 'asset/geoxml3/polys/geoxml3.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
