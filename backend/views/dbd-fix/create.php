<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\DbdFix */

$this->title = 'Create Dbd Fix';
$this->params['breadcrumbs'][] = ['label' => 'Dbd Fixes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dbd-fix-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
