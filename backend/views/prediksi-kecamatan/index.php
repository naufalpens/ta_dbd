<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PrediksiKecamatanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Prediksi Demam Berdarah';
$this->params['breadcrumbs'][] = $this->title;

$baseUrl = Yii::$app->urlManager->createAbsoluteUrl(['/']);
?>
<div class="prediksi-kecamatan-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <!--<form method="GET" action="prediksi-kecamatan/get-prediksi">-->
    <form method="GET" action="<?php echo $baseUrl.'prediksi-kecamatan/get-prediksi'; ?>">
        <div class="row col-md-12">

            <div class="form-group col-md-3">
                <label>Pilih Kecamatan</label>
                <select class="form-control" name="kecamatan">
                    <option id="kecamatan" value="kecamatan">Tiap Kecamatan</option>
                    <option id="jember" value="jember">Seluruh Jember</option>
                </select>
            </div>
            
            <div class="form-group col-md-3">
                <label>Pilih Metode Perhitungan</label>
                <select class="form-control" name="metodeperhitungan">
                    <option value="linear">Linear</option>
                    <option value="kuadratik">Kuadratik</option>
                </select>
            </div>

            <div class="form-group col-md-3">
                <label>Pilih Metode Bilangan Acak</label>
                <select class="form-control" name="metodebilacak">
                    <option value="biasa">Bilangan Acak Biasa</option>
                    <option value="ratanol">Bilangan Acak Rata-rata 0</option>
                    <!--<option value="distribusi">Bilangan Acak Distribusi Normal</option>-->
                </select>
            </div>
            
            <div class="form-group col-md-3">
                <label>Pilih Bulan</label>
                <select class="form-control" name="bulan">
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
            </div>

            <div class="form-group col-md-6">
                <input type="submit" class="btn btn-primary" value="Hitung Prediksi">
            </div>
        </div>
    </form>

    <!--<?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'id_kecamatan',
            'kasus',
            'prediksi',
            'error',
            'status',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>-->
</div>
