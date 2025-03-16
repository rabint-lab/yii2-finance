<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model rabint\finance\models\WalletConnection */

$this->title = Yii::t('rabint', 'Update') .  ' ' . Yii::t('rabint', 'دسترسی کیف پول') . ' «' . $model->id .'»';
$this->params['breadcrumbs'][] = ['label' => Yii::t('rabint', 'Wallet Connections'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rabint', 'Update');
?>

<div class="box-form wallet-connection-update"  id="ajaxCrudDatatable">

    <h2 class="ajaxModalTitle" style="display: none"><?=  $this->title; ?></h2>
    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
