<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Dbd;

/**
 * DbdSearch represents the model behind the search form about `backend\models\Dbd`.
 */
class DbdSearch extends Dbd
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ch', 'hh', 'abj', 'hi', 'kasus'], 'integer'],
            [['tanggal', 'kecamatan'], 'safe'],
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Dbd::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'tanggal' => $this->tanggal,
            'ch' => $this->ch,
            'hh' => $this->hh,
            'abj' => $this->abj,
            'hi' => $this->hi,
            'kasus' => $this->kasus,
        ]);

        $query->andFilterWhere(['like', 'kecamatan', $this->kecamatan]);

        return $dataProvider;
    }
}
