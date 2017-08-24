<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\PrediksiKecamatan */

$this->title = 'Create Prediksi Kecamatan';
$this->params['breadcrumbs'][] = ['label' => 'Prediksi Kecamatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prediksi-kecamatan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
