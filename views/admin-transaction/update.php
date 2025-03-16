<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model rabint\finance\models\FinanceTransactions */

$this->title = Yii::t('rabint', 'Update') .  ' ' . Yii::t('rabint', 'صورتحساب') . ' «' . $model->id .'»';
$this->params['breadcrumbs'][] = ['label' => Yii::t('rabint', 'Finance Transactions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rabint', 'Update');
?>

<div class="box-form finance-transactions-update"  id="ajaxCrudDatatable">

    <h2 class="ajaxModalTitle" style="display: none"><?=  $this->title; ?></h2>
    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
