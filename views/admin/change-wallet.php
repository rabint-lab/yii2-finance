<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model rabint\ticket\models\Ticket */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('rabint', 'افزودن و کاهش موجودی');
$this->params['breadcrumbs'][] = $this->title;
$money_unit = \rabint\helpers\currency::title();
?>

<div class="ticket-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="col-sm-6">
        <?php
        $model->change_action = 0;
        ?>
        <?= $form->field($model, 'change_action')->radioList([0 => 'کسر از حساب', 1 => 'افزایش حساب'], ['checked' => 0])->label('عملیات'); ?>
    </div>
    <div class="clearfix"></div>
    <div class="col-sm-6">
        <?php
        $recipient = yii\helpers\ArrayHelper::map(\common\models\User::find()->all(), 'id', 'username');
        echo $form->field($model, 'user_id')->widget(kartik\select2\Select2::classname(), [
            'data' => $recipient,
            'maintainOrder' => true,
            'options' => [
                'placeholder' => \Yii::t('rabint', 'نام کاربر مورد نظر را بنویسید'),
                'dir' => 'rtl',
                'multiple' => FALSE,
                'language' => 'fa',
                'lang' => 'fa'
            ],
            'pluginOptions' => [
                'tags' => FALSE,
                'maximumInputLength' => 1000,
                'language' => 'fa',
                'lang' => 'fa'
            ],
        ]);
        ?>
    </div>
    <div class="clearfix"></div>
    <div class="col-sm-6">
        <?= $form->field($model, 'amount')->textInput(['maxlength' => true])->label("مبلغ ($money_unit)"); ?>
    </div>
    <div class="clearfix"></div>
    <div class="col-sm-6">
        <?= $form->field($model, 'description')->textarea(); ?>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-sm-12">
        <?= Html::submitButton(Yii::t('rabint', 'ثبت'), ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
