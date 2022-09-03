<?php

use yii\bootstrap4\Html;


/* @var $this yii\web\View */
/* @var $model rabint\finance\models\FinanceOfflinePay */


$this->title = Yii::t('rabint', 'Create') .  ' ' . Yii::t('rabint', 'Finance Offline Pay') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rabint', 'Finance Offline Pays'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box-form finance-offline-pay-create"  id="ajaxCrudDatatable">

    <h2 class="ajaxModalTitle" style="display: none"><?=  $this->title; ?></h2>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
