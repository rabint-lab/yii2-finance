<?php

use rabint\finance\models\WalletConnection;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model rabint\finance\models\WalletConnection */
/* @var $form yii\widgets\ActiveForm */
$isModalAjax = Yii::$app->request->isAjax;

$this->context->layout = "@themeLayouts/full";
$aday = 60 * 60 * 24;
$expires = [
    (time() + ($aday * 365)) => Yii::t('app', 'یک سال'),
    (time() + ($aday * 30)) => Yii::t('app', 'یک ماه'),
    (time() + ($aday)) => Yii::t('app', 'یک روز'),
];
?>
<?php $form = ActiveForm::begin(); ?>


    <div class="clearfix"></div>
    <div class="form-block wallet-connection-form">
        <div class="row">
            <div class="col-sm-<?= $isModalAjax ? '12' : '6'; ?>">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card block block-rounded <?= $isModalAjax ? 'ajaxModalBlock' : ''; ?>">
                            <div class="card-header block-header block-header-default">
                                <h3 class="block-title"><?= Html::encode($this->title) ?></h3>
                            </div>

                            <div class="card-body block-content">

                                <div class="">
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
                                <?= $form->field($model, 'provider')->dropDownList(\yii\helpers\ArrayHelper::getColumn(WalletConnection::providers(), 'title')) ?>

                                <?= $form->field($model, 'expire_date')->dropDownList($expires) ?>

                                <div class="card-body block-content block-content-full">
                                    <div class="text-center">
                                        <?= Html::submitButton($model->isNewRecord ? Yii::t('rabint', 'Create') : Yii::t('rabint', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success btn-flat' : 'btn btn-primary btn-flat']) ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php ActiveForm::end(); ?>