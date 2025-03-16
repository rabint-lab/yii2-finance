<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('rabint', 'افزودن موجودی');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="block">
    <div class="card-body block-content">

        <?php $form = ActiveForm::begin(); ?>
        <div class="col-sm-12 col-md-6 offset-md-3">
            <?= $form->field($model, 'amount')->input('text', ['class' => 'form-control ltrCenter', 'data-formatter' => 'money'])->label('مبلغ به ریال'); ?>
            <div class="form-group col-sm-12 center">
                <?= Html::submitButton(Yii::t('rabint', 'پرداخت'), ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
        <div class="clearfix"></div>
        <!--        <div class="col-sm-6">
        <?php //= $form->field($model, 'description')->textarea();  ?>
                </div>-->
        <!--<div class="clearfix"></div>-->

        <?php ActiveForm::end(); ?>
    </div>
</div>
