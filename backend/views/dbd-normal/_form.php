<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\DbdNormal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="dbd-normal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tanggal')->textInput() ?>

    <?= $form->field($model, 'id_kecamatan')->textInput() ?>

    <?= $form->field($model, 'ch')->textInput() ?>

    <?= $form->field($model, 'hh')->textInput() ?>

    <?= $form->field($model, 'abj')->textInput() ?>

    <?= $form->field($model, 'hi')->textInput() ?>

    <?= $form->field($model, 'kasus')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
