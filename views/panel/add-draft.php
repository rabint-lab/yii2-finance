<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

$this->title = Yii::t('rabint', 'ثبت حواله ، چک و فقش بانکی');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="block">
    <div class="card-body block-content">

        <?php $form = ActiveForm::begin(); ?>
        <div class="col-sm-6">
            <?= $form->field($model, 'bank')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'form_id')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-6">
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        </div>
        <div class="col-sm-6">
            <?php echo rabint\helpers\widget::uploader($form, $model, 'check_url', ['returnType' => 'path', 'url' => ['/finance/panel/check-upload']]);
            ?>
        </div>
        <div class="clearfix"></div>
        <div class="form-group col-sm-12 center">
            <?= Html::submitButton(Yii::t('rabint', 'ثبت'), ['class' => 'btn btn-warning']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
