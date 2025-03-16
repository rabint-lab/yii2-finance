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
    <div class="card ">
        <div class="card-header">
            <h3 class="card-title"><?= Yii::t('rabint', 'Search') ?></h3>
        </div>
        <div class="card-body">

            <div class="search_box finance-transactions-search">

                <div class="row">

                    <div class="col-md-4"><?= $form->field($model, 'id') ?></div>

                    <div class="col-md-4"><?= $form->field($model, 'created_at') ?></div>

                    <div class="col-md-4"><?= $form->field($model, 'amount') ?></div>

                    <div class="col-md-4"><?= $form->field($model, 'status') ?></div>

                    <!--<div class="col-md-4"><?php // echo $form->field($model, 'gateway') ?></div>-->

                    <!--<div class="col-md-4"><?php // echo $form->field($model, 'gateway_reciept') ?></div>-->

                    <!--<div class="col-md-4"><?php // echo $form->field($model, 'gateway_meta') ?></div>-->

                    <!--<div class="col-md-4"><?php // echo $form->field($model, 'transactioner_ip') ?></div>-->

                    <!--<div class="col-md-4"><?php // echo $form->field($model, 'internal_reciept') ?></div>-->

                    <!--<div class="col-md-4"><?php // echo $form->field($model, 'token') ?></div>-->

                    <!--<div class="col-md-4"><?php // echo $form->field($model, 'return_url') ?></div>-->

                    <!--<div class="col-md-4"><?php // echo $form->field($model, 'additional_rows') ?></div>-->

                    <!--<div class="col-md-4"><?php // echo $form->field($model, 'metadata') ?></div>-->

                </div>

            </div>
        </div>
        <div class="card-footer">
            <?php // echo Html::resetButton(Yii::t('rabint', 'Reset'), ['class' => 'btn btn-outline-secondary float-start']) ?>
            <?= Html::a(Yii::t('rabint', 'Reset'), ['index'], ['class' => 'btn btn-outline-secondary float-start']) ?>
            <?= Html::submitButton(Yii::t('rabint', 'Search'), ['class' => 'btn btn-primary float-start']) ?>
            <div class="clearfix"></div>
        </div>
    </div>
<?php ActiveForm::end(); ?>