<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DbdNormalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Dbd Normals';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dbd-normal-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Dbd Normal', ['create'], ['class' => 'btn btn-success']) ?>        
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'tanggal',
//            'id_kecamatan',
            'kecamatan.nama_kecamatan',
            'ch',
            'hh',
            'abj',
            'hi',
            'kasus',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
