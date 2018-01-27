<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PrediksiKecamatanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Prediksi Demam Berdarah';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prediksi-kecamatan-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <form method="GET" action="prediksi-kecamatan/get-prediksi">
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
