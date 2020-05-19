<?php

namespace rabint\finance;

use Yii;
use rabint\finance\models\FinanceTransactions;
use rabint\finance\models\FinanceWallet;

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
            'icon' => '<i class="fas fa-credit-card"></i>',
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
                /*           [
                    'label' => Yii::t('rabint', 'حواله های ثبت شده'),
                    'url' => ['/finance/admin-draft'],
                    'icon' => '<i class="far fa-circle"></i>',
                ],
                */
            ]
        ];
    }

    public static function dashboardMenu_()
    {
        return [
            'label' => Yii::t('rabint', 'مالی'),
            'url' => '#',
            'icon' => '<i class="fas fa-credit-card"></i>',
            'options' => ['class' => 'treeview'],
            'hit' => \Yii::t('rabint', 'این بخش مربوط به مدیریت مالی پروفایل شما می باشد'),
            'items' => [
                [
                    'label' => Yii::t('rabint', 'صورتحساب  ها'),
                    'url' => ['/finance/transaction'],
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
                    'label' => Yii::t('rabint', 'حواله های ثبت شده'),
                    'url' => ['/finance/admin-draft'],
                    'icon' => '<i class="far fa-circle"></i>',
                ],
            ]
        ];
    }

    /* =================================================================== */
    /* ################################################################### */
    /* =================================================================== */

    public static function pay($args)
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
            'settleCallback'=>[],
        ], $args);
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
        $transaction = new FinanceTransactions;
        $transaction->created_at = time();
        $transaction->transactioner = $user_id;
        $transaction->amount = $args['amount'];
        $transaction->status = FinanceTransactions::TRANSACTION_SKIPPED;
        $transaction->gateway = -1;
        $transaction->gateway_reciept = NULL;
        $transaction->gateway_meta = NULL;
        $transaction->transactioner_ip = Yii::$app->getRequest()->getUserIP();
        $transaction->internal_reciept = $args['internal_reciept'];
        $transaction->token = md5(uniqid('FinanceTransactions', TRUE));
        $transaction->return_url = $args['return_url'];
        $transaction->additional_rows = json_encode($args['additional_rows']);
        $transaction->metadata = json_encode($args['metadata']);
        $transaction->settle_callback_function = json_encode($args['settleCallback']);
        //$transaction->metadata = NULL;
        /* =================================================================== */
        /**
         * check must use whalet ?
         */
        if (
            $user_id > 0 and
            $walletCredit >= $args['amount'] and
            FinanceTransactions::ALLOW_USE_WALLET and (!$args['forcePay'])
        ) {
            /**
             * using walet
             */
            $transaction->status = FinanceTransactions::TRANSACTION_COMPLETED;
            $transaction->gateway = 0;
            if (!$transaction->save()) {
                return FALSE;
            }
            $_SESSION['finance']['id'] = $transaction->id;
            /* pay with wallet -------------------------------------- */
            //            FinanceWallet::dec(
            //                    $transaction->transactioner, $transaction->amount, $transaction->transactioner, $transaction->transactioner_ip, 'پرداخت با کیف پول', ['transaction_id' => $transaction->id]
            //            );
            $balancingRes = FinanceWallet::balancingPay(
                $transaction->transactioner,
                $args['additional_rows'],
                $transaction->transactioner,
                $transaction->transactioner_ip
            );
            $_SESSION['finance']['pay_status'] = FinanceTransactions::TRANSACTION_COMPLETED;
            $_SESSION['finance']['transactionID'] = $transaction->id;
            /* ------------------------------------------------------ */
            $returnUrl = $transaction->return_url;
            //            if (strpos($returnUrl, '?') !== FALSE) {
            //                $returnUrl .='&tid=' . $transaction->id;
            //            } else {
            //                $returnUrl .='?tid=' . $transaction->id;
            //            }
            //            die('SS.'.$returnUrl);
            return redirect($returnUrl);
        } else {
            if ($args['showFacture'] or FinanceTransactions::GATEWAY_SELECT_METHOD == 'manual') {
                $transaction->status = FinanceTransactions::TRANSACTION_PENDING;
                if (!$transaction->save(false)) {
                    return FALSE;
                }
                \rabint\helpers\uri::redirect(['/finance/transaction/view', 'id' => $transaction->id]);
                return;
            } else {
                $selectedGateway = reset(FinanceTransactions::$paymentGateways);
            }
            $transaction->status = FinanceTransactions::TRANSACTION_INPROCESS;
            $transaction->gateway = key(FinanceTransactions::$paymentGateways);
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
    }

    /* =================================================================== */

    public static function getTransactionStatus($tid)
    {
        $model = FinanceTransactions::findOne($tid);
        if (empty($model))
            return FALSE;
        $status = (object) [
            'amount' => $model->amount,
            'status' => $model->status,
            'trackingCode' => $model->gateway_reciept,
            'internalReciept' => $model->internal_reciept,
            'transactioner' => $model->transactioner,
        ];
        return $status;
    }
    public static function getTransactionStatusByReciept($rid)
    {
        $model = FinanceTransactions::findOne([
            'internal_reciept'=>$rid
        ]);
        if (empty($model))
            return FALSE;
        $status = (object) [
            'amount' => $model->amount,
            'status' => $model->status,
            'trackingCode' => $model->gateway_reciept,
            'internalReciept' => $model->internal_reciept,
            'transactioner' => $model->transactioner,
        ];
        return $status;
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
        $status = (object) [
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
    public static function numberToCurrency($number,$currency = null, $addPostFix = true)
    {
        return number_format($number) . ' ' .  \Yii::t('rabint', 'تومان');
    }
}
