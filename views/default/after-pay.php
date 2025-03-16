<?php

/* @var $this yii\web\View */

/* @var $transaction \rabint\finance\models\FinanceTransactions */
/* @var $pay_result bool */
/* @var $message string */

$this->title = Yii::t('rabint', 'پرداخت صورتحساب');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grid-box finance-index">
    <div class="clearfix"></div>

    <?php

    use yii\helpers\Html;

    ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title"><?= ($pay_result) ? Yii::t('app', 'صورتحساب شما پرداخت شد') : \Yii::t('app', 'خطا در پرداخت صورتحساب'); ?></h3>
                    </div>
                    <div class="card-body">
                        <?php if ($pay_result): ?>
                            <div class="alert alert-success" role="alert">
                                <?= Html::encode($message) ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger" role="alert">
                                <?= Html::encode($message) ?>
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                <!--                                <tr>-->
                                <!--                                    <th scope="row">شماره رسید داخلی</th>-->
                                <!--                                    <td>-->
                                <?php //= Html::encode($transaction->internal_reciept) ?><!--</td>-->
                                <!--                                </tr>-->
                                <!--                                <tr>-->
                                <!--                                    <th scope="row">عنوان تراکنش</th>-->
                                <!--                                    <td>-->
                                <?php //= Html::encode($transaction->metadata) ?><!--</td>-->
                                <!--                                </tr>-->
                                <!--                                <tr>-->
                                <!--                                    <th scope="row">درگاه پرداخت</th>-->
                                <!--                                    <td>-->
                                <?php //= Html::encode($transaction->gateway) ?><!--</td>-->
                                <!--                                </tr>-->
                                <!--                                <tr>-->
                                <!--                                    <th scope="row">شماره رسید درگاه</th>-->
                                <!--                                    <td>-->
                                <?php //= Html::encode($transaction->gateway_reciept) ?><!--</td>-->
                                <!--                                </tr>-->
                                <tr>
                                    <th scope="row">مبلغ تراکنش</th>
                                    <td><?= number_format($transaction->amount) . ' ' . Yii::t('app', 'ریال'); ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>