<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model rabint\finance\models\FinanceTransactionsSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin([
//'action' => ['index'],
'method' => 'get',
]); ?>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?= Yii::t('rabint', 'Search') ?></h3>
    </div>
    <div class="panel-body">

        <div class="search_box finance-transactions-search">

            <div class="row">

                        <div class="col-sm-4"><?= $form->field($model, 'id') ?></div>

        <div class="col-sm-4"><?= $form->field($model, 'created_at') ?></div>

        <div class="col-sm-4"><?= $form->field($model, 'transactioner') ?></div>

        <div class="col-sm-4"><?= $form->field($model, 'amount') ?></div>

        <div class="col-sm-4"><?= $form->field($model, 'status') ?></div>

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'gateway') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'gateway_reciept') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'gateway_meta') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'transactioner_ip') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'internal_reciept') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'token') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'return_url') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'additional_rows') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'metadata') ?></div>-->


            </div>

        </div>
    </div>
    <div class="panel-footer">
        <?php // echo Html::resetButton(Yii::t('rabint', 'Reset'), ['class' => 'btn btn-default pull-left']) ?>
        <?= Html::a(Yii::t('rabint', 'Reset'), ['index'], ['class' => 'btn btn-default pull-left']) ?>
        <?= Html::submitButton(Yii::t('rabint', 'Search'), ['class' => 'btn btn-primary pull-left']) ?>
        <div class="clearfix"></div>
    </div>
</div>
<?php ActiveForm::end(); ?>