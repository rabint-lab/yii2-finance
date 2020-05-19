<?php

namespace rabint\finance\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use rabint\finance\models\FinanceTransactions;

/**
 * FinanceTransactionsSearch represents the model behind the search form about `\rabint\finance\models\FinanceTransactions`.
 */
class FinanceTransactionsSearch extends FinanceTransactions
{

    //var $keyword;
    //var $created_from;
    //var $created_to;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'transactioner', 'amount', 'status', 'gateway'], 'integer'],
            [['gateway_reciept', 'gateway_meta', 'transactioner_ip', 'internal_reciept', 'token', 'return_url', 'additional_rows', 'metadata'], 'safe'],
            //[['created_from', 'created_to', 'keyword'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return parent::attributeLabels() + [
        //    'created_from' =>  Yii::t('rabint', 'Created from'),
        //    'created_to' =>  Yii::t('rabint', 'Created to'),
        //   'keyword' =>  Yii::t('rabint', 'Keyword'),
        ];
    }
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param boolean $returnActiveQuery
     *
     * @return ActiveDataProvider | ActiveQuery
     */
    public function search($params,$returnActiveQuery = FALSE)
    {
        $query = FinanceTransactions::find();//->alias('financetransactions');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'transactioner' => $this->transactioner,
            'amount' => $this->amount,
            'status' => $this->status,
            'gateway' => $this->gateway,
        ]);

        $query->andFilterWhere(['like', 'gateway_reciept', $this->gateway_reciept])
            ->andFilterWhere(['like', 'gateway_meta', $this->gateway_meta])
            ->andFilterWhere(['like', 'transactioner_ip', $this->transactioner_ip])
            ->andFilterWhere(['like', 'internal_reciept', $this->internal_reciept])
            ->andFilterWhere(['like', 'token', $this->token])
            ->andFilterWhere(['like', 'return_url', $this->return_url])
            ->andFilterWhere(['like', 'additional_rows', $this->additional_rows])
            ->andFilterWhere(['like', 'metadata', $this->metadata]);


        //if (!empty($this->keyword)) {
        //    $query->andFilterWhere([
        //        'OR',
        //        ['title'=>$this->keyword],
        //        //['decription'=>$this->keyword],
        //    ]);
        //
        //    //$exp1 = new \yii\db\Expression(
        //    //        "id in (SELECT user_id from user_profile  WHERE " .
        //    //        //  "firstname like '%:keyword%' or  ".
        //    //        //  "lastname like '%:keyword%' or  ".
        //    //        "nickname like ':keyword')", 
        //    //     ['keyword' => '%'.$this->keyword.'%']);
        //    //$query->andWhere($exp1);
        //}

        /**
         * date filters:
         */
        //if (!empty($this->created_at)) {
        //    $from = locality::anyToGregorian($this->created_at);
        //    $to = locality::anyToGregorian($this->created_at+86400);
        //    $query->andFilterWhere(['>=', 'created_at', $from]);
        //    $query->andFilterWhere(['<=', 'created_at', $to]);
        //}
        //
        //if (!empty($this->created_from)) {
        //    $this->created_from = locality::anyToGregorian($this->created_from);
        //    $query->andFilterWhere(['>=', 'created_at', $this->created_from]);
        //}
        //if (!empty($this->created_to)) {
        //    $this->calldate_ = locality::anyToGregorian($this->created_to);
        //    $query->andFilterWhere(['<=', 'created_at', $this->created_to]);
        //}
        


        if ($returnActiveQuery) {
            return $query;
        }
        return $dataProvider;
    }
}
