<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\DbdSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="dbd-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'tanggal') ?>

    <?= $form->field($model, 'kecamatan') ?>

    <?= $form->field($model, 'ch') ?>

    <?= $form->field($model, 'hh') ?>

    <?php // echo $form->field($model, 'abj') ?>

    <?php // echo $form->field($model, 'hi') ?>

    <?php // echo $form->field($model, 'kasus') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
