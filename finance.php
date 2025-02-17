<?php

namespace rabint\finance;

use rabint\finance\addons\WalletGateway;
use rabint\finance\models\FinanceTransactions;
use rabint\finance\models\FinanceWallet;
use Yii;
use yii\base\InvalidConfigException;

class finance extends \yii\base\Module
{

    const CURRENCY_IR_TOMAN = 'IR_TOMAN';
    const CURRENCY_IR_RIAL = 'IR_RIAL';
    const CURRENCY_US_DOLAR = 'US_DOLAR';

    public static $MASTER_CURRENCY = 'IR_RIAL';
    public static $CURRENT_CURRENCY = 'IR_RIAL';

    /* ------------------------------------------------------ */
    public $controllerNamespace = 'rabint\finance\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public static function adminMenu()
    {
        return [
            'label' => Yii::t('rabint', 'مالی'),
            'url' => '#',
            'visible' => \rabint\helpers\user::can('manager'),
            'icon' => '<i class="fa fa-credit-card"></i>',
            'options' => ['class' => 'treeview'],
            'items' => [
                [
                    'label' => Yii::t('rabint', 'صورتحساب  ها'),
                    'url' => ['/finance/admin-transaction'],
                    'icon' => '<i class="far fa-circle"></i>',
                ],
                [
                    'label' => Yii::t('rabint', 'تراکنش ها'),
                    'url' => ['/finance/admin/wallet'],
                    'icon' => '<i class="far fa-circle"></i>',
                ],
                [
                    'label' => Yii::t('rabint', 'افزودن و کاهش وجه'),
                    'url' => ['/finance/admin/change-wallet'],
                    'icon' => '<i class="far fa-circle"></i>',
                ],
                [
                    'label' => Yii::t('rabint', 'پرداخت های آفلاین'),
                    'url' => ['/finance/admin-finance-offline-pay'],
                    'icon' => '<i class="far fa-circle"></i>',
                ],
                [
                    'label' => Yii::t('rabint', 'کیف پول های متصل'),
                    'url' => ['/finance/admin-wallet-connection'],
                    'icon' => '<i class="far fa-circle"></i>',
                ],
                /*           [
                    'label' => Yii::t('rabint', 'حواله های ثبت شده'),
                    'url' => ['/finance/admin-draft'],
                    'icon' => '<i class="far fa-circle"></i>',
                ],
                */
            ]
        ];
    }

    public static function dashboardMenu()
    {
        $cash = \rabint\finance\models\FinanceWallet::cash(\rabint\helpers\user::id());
        if (config('SERVICE.finance.showDashboardMenu', true)) {
            return [
                'label' => Yii::t('rabint', 'مالی'),
                'url' => '#',
                'icon' => '<i class="fa fa-credit-card"></i>',
                'options' => ['class' => 'treeview'],
                'hit' => \Yii::t('app', 'موجودی حساب شما: ') . number_format($cash) . Yii::t('app', 'ریال'),
                'items' => [
                    //                [
                    //                    'label' => Yii::t('rabint', 'صورتحساب  ها'),
                    //                    'url' => ['/finance/transaction'],
                    //                    'icon' => '<i class="far fa-circle"></i>',
                    //                ],
                    [
                        'label' => Yii::t('rabint', 'سوابق مالی'),
                        'url' => ['/finance/panel/wallet'],
                        'icon' => '<i class="far fa-circle"></i>',
                    ],
                    [
                        'label' => Yii::t('rabint', 'شارژ حساب'),
                        'url' => ['/finance/panel/charge'],
                        'icon' => '<i class="far fa-circle"></i>',
                    ],
                    //                [
                    //                    'label' => Yii::t('rabint', 'حواله های ثبت شده'),
                    //                    'url' => ['/finance/admin-draft'],
                    //                    'icon' => '<i class="far fa-circle"></i>',
                    //                ],
                ]
            ];
        } else {
            return [];
        }
    }

    /* =================================================================== */
    /* ################################################################### */
    /* =================================================================== */

    /**
     * @return int
     */
    public static function preparePay()
    {
        if (Yii::$app->user->isGuest) {
            $walletCredit = 0;
            $user_id = 0;
        } else {
            $user_id = Yii::$app->user->identity->id;
            $walletCredit = FinanceWallet::credit($user_id);
        }

        $transaction = new FinanceTransactions;
        $transaction->created_at = time();
        $transaction->transactioner = $user_id;
        $transaction->amount = 0;
        $transaction->status = FinanceTransactions::TRANSACTION_PENDING;
        $transaction->transactioner_ip = Yii::$app->getRequest()->getUserIP();
        $transaction->internal_reciept = '';
        $transaction->token = md5(uniqid('FinanceTransactions', TRUE));
        $transaction->return_url = '';
        $transaction->additional_rows = '--';
        $transaction->save(false);

        return $transaction->id;
    }

    /**
     * @param array $args
     * @param FinanceTransactions $transModel
     * @return bool|void
     * @throws InvalidConfigException
     */
    public static function pay($args, $transModel = null)
    {
        $args = array_merge([
            'amount' => 0,
            'internal_reciept' => '',
            'return_url' => '',
            'internal_meta' => NULL,
            'additional_rows' => [],
            'metadata' => [],
            'description' => '',
            'forcePay' => false,
            'showFacture' => false,
            'settleCallback' => [],
        ], $args);

        if (!empty($args['additional_rows']) && !FinanceWallet::validateAdditionalRows($args['additional_rows'])) {
            throw new InvalidConfigException('additional rows config error, it must has amount,description,user_id ');
        }

        if(!isset($row['amount'])){
            $err++;
        }
        if(!isset($row['description'])){
            $err++;
        }
        if(!isset($row['user_id'])){
            $err++;
        }

        if ($args['forcePay']) {
            $args['showFacture'] = false;
        }
        $_SESSION['finance']['pay_status'] = FinanceTransactions::TRANSACTION_SKIPPED;

        if (
            empty($args['amount']) or
            empty($args['internal_reciept']) or
            empty($args['return_url'])
        ) {
            return FALSE;
        }
        if (Yii::$app->user->isGuest) {
            $walletCredit = 0;
            $user_id = 0;
        } else {
            $user_id = Yii::$app->user->identity->id;
            $walletCredit = FinanceWallet::credit($user_id);
        }
        /* =================================================================== */
        if ($transaction != null) {
            $transaction = $transModel;
        } else {
            $transaction = new FinanceTransactions;
        }
        $transaction->created_at = time();
        $transaction->transactioner = $user_id;
        $transaction->amount = $args['amount'];
        if (Config::TAX_PERCENT) {
            $transaction->amount = $args['amount'] = $transaction->amount + ($transaction->amount * Config::TAX_PERCENT / 100);
        }
        $transaction->status = FinanceTransactions::TRANSACTION_SKIPPED;
        $transaction->gateway = -1;
        $transaction->gateway_reciept = NULL;
        $transaction->gateway_meta = NULL;
        $transaction->transactioner_ip = Yii::$app->getRequest()->getUserIP();
        $transaction->internal_reciept = $args['internal_reciept'];
        $transaction->token = md5(uniqid('FinanceTransactions', TRUE));
        $transaction->return_url = $args['return_url'];
        $transaction->internal_meta = $args['internal_meta'];
        $transaction->additional_rows = json_encode($args['additional_rows']);
        $transaction->metadata = json_encode($args['metadata']);
        $transaction->settle_callback_function = json_encode($args['settleCallback']);
        //$transaction->metadata = NULL;
        /* =================================================================== */
        /**
         * check must use whalet ?
         */

        if (
            FinanceTransactions::AUTO_PAY_BY_WALLET and
            $user_id > 0 and
            $walletCredit >= $args['amount'] and
            FinanceTransactions::ALLOW_USE_WALLET and (!$args['forcePay'])
        ) {
            /**
             * using walet
             */
            $selectedGateway = WalletGateway::class;

            $callbackUrl = \yii\helpers\Url::to(['/finance/default/afterpay', 'tid' => $transaction->id, 'token' => $transaction->token], TRUE);

            $transaction->status = FinanceTransactions::TRANSACTION_INPROCESS;
            $transaction->gateway = 0;
            if ($model->save(false)) {
                $gatewayClass = new $selectedGateway;
                $error = $gatewayClass->startPay($model->id, $model->amount, $callbackUrl);
                if ($error == $gatewayClass->gatewaySuccessStatus) {
                    return TRUE;
                }
            }
        }

        if (!$args['forcePay'] && ($args['showFacture'] or FinanceTransactions::GATEWAY_SELECT_METHOD == 'manual')) {
            $transaction->status = FinanceTransactions::TRANSACTION_PENDING;
            if (!$transaction->save(false)) {
                return FALSE;
            }
            \rabint\helpers\uri::redirect(['/finance/transaction/view', 'id' => $transaction->id]);
            return;
        } else {
            $defaultGatewayId = FinanceTransactions::defaultPaymentGatewayId();
            $selectedGateway = FinanceTransactions::paymentGateways()[$defaultGatewayId];
        }
        $transaction->status = FinanceTransactions::TRANSACTION_INPROCESS;
        $transaction->gateway = FinanceTransactions::defaultPaymentGatewayId();
        if (!$transaction->save(false)) {
            return FALSE;
        }

        $gateway = new $selectedGateway['class'];
        $callbackUrl = \yii\helpers\Url::to(['/finance/default/afterpay', 'tid' => $transaction->id, 'token' => $transaction->token], TRUE);
        $error = $gateway->startPay($transaction->id, $args['amount'], $callbackUrl);
        if ($error == $gateway->gatewaySuccessStatus) {
            return TRUE;
        } else {
            if (isset($gateway->messages[$error])) {
                Yii::$app->session->setFlash('warning', $gateway->messages[$error]);
            }
            return FALSE;
        }
    }

    /* =================================================================== */

    public static function getTransactionStatus($tid)
    {
        $model = FinanceTransactions::findOne($tid);
        if (empty($model))
            return FALSE;
        $status = (object)[
            'amount' => $model->amount,
            'status' => $model->status,
            'trackingCode' => $model->gateway_reciept,
            'internalReciept' => $model->internal_reciept,
            'transactioner' => $model->transactioner,
        ];
        return $status;
    }

    public static function getTransactionStatusByReciept($rid, $tid = null)
    {
        $where['internal_reciept'] = $rid;
        if (!empty($tid)) {
            $where['id'] = $tid;
        }
        return FinanceTransactions::findOne($where);

//        if ($model==null){
//            return FALSE;
//        }
//        return $model;
//        $status = (object)[
//            'amount' => $model->amount,
//            'status' => $model->status,
//            'trackingCode' => $model->gateway_reciept,
//            'internalReciept' => $model->internal_reciept,
//            'transactioner' => $model->transactioner,
//        ];
    }

    public static function getLatestTransactionStatus($removeSession = TRUE)
    {
        if (!isset($_SESSION['finance']['pay_status']) or !isset($_SESSION['finance']['transactionID'])) {
            return false;
        }
        if ($_SESSION['finance']['pay_status'] != \rabint\finance\Config::TRANSACTION_COMPLETED) {
            return false;
        }
        /* ------------------------------------------------------ */
        $model = FinanceTransactions::findOne($_SESSION['finance']['transactionID']);
        if (empty($model)) {
            return FALSE;
        }
        if ($removeSession) {
            unset($_SESSION['finance']);
        }
        /* ------------------------------------------------------ */
        $status = (object)[
            'transactionId' => $model->id,
            'amount' => $model->amount,
            'status' => $model->status,
            'trackingCode' => $model->gateway_reciept,
            'internalReciept' => $model->internal_reciept,
            'transactioner' => $model->transactioner,
            'transactionTime' => $model->created_at,
        ];
        return $status;
    }

    /* =================================================================== */

    public static function currencies()
    {
        return [
            static::CURRENCY_IR_RIAL => ['factor' => 1, 'title' => \Yii::t('rabint', 'ریال')],
            static::CURRENCY_IR_TOMAN => ['factor' => .1, 'title' => \Yii::t('rabint', 'تومان')],
            static::CURRENCY_US_DOLAR => ['factor' => .00028572, 'title' => \Yii::t('rabint', 'دلار')],
        ];
    }

    public static function getCurrentCurrencyTitle()
    {

        return static::currencies()[static::$CURRENT_CURRENCY]['title'];
    }

    /**
     * Undocumented function
     *
     * @param [type] $number
     * @param boolean $addPostFix
     * @param [type] $currency
     * @return void
     */
    public static function numberToCurrency($number, $currency = null, $addPostFix = true)
    {
        return number_format($number) . ' ' . \Yii::t('rabint', 'تومان');
    }
}
