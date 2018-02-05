<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\NewDbd */

$this->title = 'Create New Dbd';
$this->params['breadcrumbs'][] = ['label' => 'New Dbds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="new-dbd-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
