<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\NewDbdSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Data Awal';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="new-dbd-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'tanggal',
            'kecamatan',
            'ch',
            'hh',
            'abj',
            'hi',
            'kasus',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
