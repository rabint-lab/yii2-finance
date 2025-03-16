<?php

use yii\bootstrap4\Html;


/* @var $this yii\web\View */
/* @var $model rabint\finance\models\WalletConnection */


$this->title = Yii::t('rabint', 'Create') .  ' ' . Yii::t('rabint', 'اتصال به والت') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rabint', 'Wallet Connections'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box-form wallet-connection-create"  id="ajaxCrudDatatable">

    <h2 class="ajaxModalTitle" style="display: none"><?=  $this->title; ?></h2>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
