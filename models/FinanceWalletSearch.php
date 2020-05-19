<?php

namespace rabint\finance\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use rabint\finance\models\FinanceWallet;

/**
 * FinanceWalletSearch represents the model behind the search form about `rabint\finance\models\FinanceWallet`.
 */
class FinanceWalletSearch extends FinanceWallet {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['created_at', 'user_id', 'amount', 'transactioner'], 'integer'],
            [['transactioner_ip', 'description', 'metadata'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = FinanceWallet::find();

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

        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'transactioner' => $this->transactioner,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'transactioner_ip', $this->transactioner_ip])
                ->andFilterWhere(['like', 'metadata', $this->metadata]);

        return $dataProvider;
    }

}
