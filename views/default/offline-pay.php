<?php
$form = \yii\widgets\ActiveForm::begin();
?>
<div class="cantiner">
    <div class="row">
        <div class="col-sm-12"><h3><?=Yii::t('app','ثبت اطلاعات پرداخت')?></h3></div>

        <div class="col-sm-12"><span><?=Yii::t('rabint','لطفا مبلغ {amount} ریال را به شماره کارت {cart_id} بانک {bank} به نام {name} واریز نمایید واطلاعات را در این قسمت وارد کنید.',['amount'=>$model->amount,'cart_id'=>config('card.id'),'name'=>config('card.name'),'bank'=>config('card.bank')])?></span></div>
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-12"><?=$form->field($model,'tracking_cod')?></div>
                <div class="col-sm-12"><?=rabint\helpers\widget::datePicker($form,$model,'date_pay')?></div>
                <div class="col-sm-12"><?=$form->field($model,'description')->textarea()->hint(Yii::t('rabint','توضیحات خاص(غیر اجباری)'))?></div>
            </div>
        </div>
        <div class="col-sm-6">
            <label for="event-end_register"><?= Yii::t('app', 'تصویر فیش واریزی') ?></label>
            <?= \rabint\helpers\widget::uploader($form, $model, 'image', ['maxNumberOfFiles' => 1,
//                    'maxFileSize' => 600 * 1024 * 1024, // 500Mb
                    'acceptFileTypes' => new \yii\web\JsExpression('/(\.|\/)(gif|png|jpe?g)$/i'),]
            );?>
            <div class="hint-block">
                <?php //= Yii::t('app', 'پوستر رویداد بصورت افقی '); ?>
            </div>

        </div>
        <?= \yii\helpers\Html::submitButton(Yii::t('rabint','ثبت'),['class'=>'btn btn-success d-block mr-auto ml-auto mb-2'])?>
    </div>
</div>
<?php
\yii\widgets\ActiveForm::end();