<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PrediksiKecamatan */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="prediksi-kecamatan-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_kecamatan')->textInput() ?>    

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
