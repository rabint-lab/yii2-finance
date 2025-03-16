<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model rabint\finance\models\FinanceTransactions */
/* @var $form yii\widgets\ActiveForm */
$isModalAjax = Yii::$app->request->isAjax;

$this->context->layout = "@themeLayouts/full";

?>
<?php $form = ActiveForm::begin(); ?>


    <div class="clearfix"></div>
    <div class="form-block finance-transactions-form">
        <div class="row">
            <div class="col-sm-<?= $isModalAjax ? '12' : '12'; ?>">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card block block-rounded <?= $isModalAjax ? 'ajaxModalBlock' : ''; ?>">
                            <div class="card-header block-header block-header-default">
                                <h3 class="block-title"><?= Html::encode($this->title) ?></h3>
                            </div>

                            <div class="card-body block-content">

                                <div class="col-sm-6">
                                    <?php
                                    $recipient = yii\helpers\ArrayHelper::map(\common\models\User::find()->all(), 'id', 'username');
                                    echo $form->field($model, 'transactioner')->widget(kartik\select2\Select2::classname(), [
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
                                    <?= $form->field($model, 'amount')->input('text', ['class' => 'form-control ltrCenter', 'data-formatter' => 'money'])->label(Yii::t('app', 'مبلغ به {unit}', ['unit' => 'ریال'])); ?>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'metadata')->textInput(); ?>
                                </div>
                                <div class="clearfix"></div>

                            </div>
                            <div class="card-body block-content block-content-full">
                                <div class="col-sm-6">
                                    <div class="text-center">
                                        <?= Html::submitButton($model->isNewRecord ? Yii::t('rabint', 'Create') : Yii::t('rabint', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success btn-flat' : 'btn btn-primary btn-flat']) ?>
                                    </div>
                                </div>
                            </div><!-- /.block-content block-content-full-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php ActiveForm::end(); ?>