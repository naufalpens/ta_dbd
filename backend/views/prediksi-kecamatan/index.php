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
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_kecamatan',
            'hasil',
            'status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
