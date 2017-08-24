<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\DbdNormal;

/**
 * DbdNormalSearch represents the model behind the search form about `backend\models\DbdNormal`.
 */
class DbdNormalSearch extends DbdNormal
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_kecamatan'], 'integer'],
            [['tanggal'], 'safe'],
            [['ch', 'hh', 'abj', 'hi', 'kasus'], 'number'],
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
        $query = DbdNormal::find();

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
            'id_kecamatan' => $this->id_kecamatan,
            'ch' => $this->ch,
            'hh' => $this->hh,
            'abj' => $this->abj,
            'hi' => $this->hi,
            'kasus' => $this->kasus,
        ]);

        return $dataProvider;
    }
}
