<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

$this->title = Yii::t('rabint', 'افزودن موجودی');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="block">
    <div class="card-body block-content">

        <?php $form = ActiveForm::begin(); ?>
        <div class="col-sm-6">
            <?= $form->field($model, 'amount')->textInput(['maxlength' => true])->label('مبلغ به تومان'); ?>
        </div>
        <div class="clearfix"></div>
        <!--        <div class="col-sm-6">
        <?php //= $form->field($model, 'description')->textarea();  ?>
                </div>-->
        <!--<div class="clearfix"></div>-->
        <div class="form-group col-sm-12 center">
            <?= Html::submitButton(Yii::t('rabint', 'پرداخت'), ['class' => 'btn btn-warning']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
