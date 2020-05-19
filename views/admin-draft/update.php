<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model rabint\finance\models\FinanceDraft */

$this->title = Yii::t('rabint', 'Update') .  ' ' . Yii::t('rabint', 'Finance Draft') . ' «' . $model->title .'»';
$this->params['breadcrumbs'][] = ['label' => Yii::t('rabint', 'Finance Drafts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rabint', 'Update');
?>

<div class="box-form finance-draft-update"  id="ajaxCrudDatatable">

    <h2 class="ajaxModalTitle" style="display: none"><?=  $this->title; ?></h2>
    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
