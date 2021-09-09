<?php

use rabint\finance\Config;
use yii\helpers\Form;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use function GuzzleHttp\json_decode;

/* @var $this yii\web\View */
/* @var $model rabint\finance\models\FinanceTransactions */

$this->title = \Yii::t('rabint', 'صورتحساب شماره :') . $model->id;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="finance-transactions-view">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="facture_info">
                <div class="row">
                    <div class="col facture_val">
                        <span class="title"> <?= \Yii::t('rabint', 'شناسه'); ?>: </span>
                        <span class="value"><?= $model->id ?></span>
                    </div>
                    <div class="col facture_val">
                        <span class="title"> <?= \Yii::t('rabint', 'تاریخ ایجاد'); ?>: </span>
                        <span class="value"><?= \rabint\helpers\locality::jdate('j F Y', $model->created_at); ?></span>
                    </div>
                    <div class="col facture_val">
                        <span class="title"><?= \Yii::t('rabint', 'وضعیت'); ?>: </span>
                        <span class="value badge badge-<?= Config::statuses()[$model->status]['class']; ?>"><?= Config::statuses()[$model->status]['title']; ?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col facture_val">
                        <span class="title"> <?= \Yii::t('rabint', 'مبلغ قابل پرداخت'); ?>: </span>
                        <td>
                            <?= \rabint\helpers\currency::numberToCurrency($model->amount, null, true) ?>
                            <?= \rabint\helpers\currency::title() ?>
                        </td>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="spacer"></div>
    <div class="spacer"></div>

    <div class="facture_items">
        <div class="row">
            <div class="col  offset-md-2 col-md-8">
                <table class="table table-striped table-bordered">
                    <thead>
                        <th><?= \Yii::t('rabint', 'ردیف'); ?></th>
                        <th><?= \Yii::t('rabint', 'شرح'); ?></th>
                        <th><?= \Yii::t('rabint', 'تعداد'); ?></th>
                        <th><?= \Yii::t('rabint', 'مبلغ واحد'); ?> (<?= \rabint\helpers\currency::title() ?>)</th>
                        <th><?= \Yii::t('rabint', 'مبلغ کل'); ?> (<?= \rabint\helpers\currency::title() ?>)</th>
                    </thead>
                    <tbody>
                        <?php
                        $rows = json_decode($model->metadata, true);
                        foreach ($rows as $k => $row) {
                        ?>
                            <tr>
                                <td><?= $k + 1; ?></td>
                                <td><?= $row[0] ?></td>
                                <td><?= $row[1] ?></td>
                                <td><?= \rabint\helpers\currency::numberToCurrency($row[2], null, true) ?> </td>
                                <td><?= \rabint\helpers\currency::numberToCurrency($row[3], null, true) ?> </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" style="text-align:left;"><span><?= \Yii::t('rabint', 'مبلغ قابل پرداخت'); ?></span></td>
                            <td colspan="3"><?= \rabint\helpers\currency::numberToCurrency($model->amount, null, true) ?> <?= \rabint\helpers\currency::title() ?> </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="spacer"></div>

    <div class="spacer"></div>

    <?php
    $form =  ActiveForm::begin();
    echo Html::hiddenInput("do_pay", 1);
    ?>

    <div class="facture_gateway">
        <div class="row">
            <div class="col offset-md-2 col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-user"></i> <?= \Yii::t('rabint', 'انتخاب درگاه پرداخت'); ?>
                        </h5>
                        <ul class="gateways" style="list-style: none">
                            <?php
                            $cash = \rabint\finance\models\FinanceWallet::cash(\rabint\helpers\user::id());
                            if ($model->amount <= $cash) {
                            ?>
                                <li>
                                    <label>
                                        <input type="radio" name="gateway" value="wallet" />
                                        <?= \Yii::t('app', 'کسر از حساب'); ?>
                                        <i style="font-size: 12px">(<?=
                                                                    \Yii::t('app', 'موجودی حساب شما: ') . number_format($cash) . Yii::t('app', 'ریال');
                                                                    ?>)</i>
                                    </label>
                                </li>
                            <?php
                            }
                            ?>
                            <?php
                            $first = true;
                            foreach (\rabint\finance\models\FinanceTransactions::paymentGateways() as $key => $gateway) {
                            ?>
                                <li>
                                    <label>
                                        <input type="radio" name="gateway" value="<?= $key; ?>" <?= $first ? 'checked  ' : ''; ?> />
                                        <?= $gateway['title']; ?>
                                    </label>
                                </li>
                            <?php
                                $first = false;
                            }
                            ?>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="spacer"></div>
    <div class="spacer"></div>
    <div class="pay">
        <div class="row">
            <div class="col-md-4 offset-md-4">
                <div class="center">
                    <button class="btn btn-success m-auto btn-lg" type="submit">
                        <?= \Yii::t('rabint', 'پرداخت'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>