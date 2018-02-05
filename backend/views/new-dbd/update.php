<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\NewDbd */

$this->title = 'Update New Dbd: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'New Dbds', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="new-dbd-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
