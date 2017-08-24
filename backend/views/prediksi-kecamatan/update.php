<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\PrediksiKecamatan */

$this->title = 'Update Prediksi Kecamatan: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Prediksi Kecamatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="prediksi-kecamatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
