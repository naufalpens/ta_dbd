<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PrediksiKecamatan;

/**
 * PrediksiKecamatanSearch represents the model behind the search form about `backend\models\PrediksiKecamatan`.
 */
class PrediksiKecamatanSearch extends PrediksiKecamatan
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_kecamatan'], 'integer'],
            [['hasil', 'status'], 'safe'],
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
        $query = PrediksiKecamatan::find();

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
            'id_kecamatan' => $this->id_kecamatan,
        ]);

        $query->andFilterWhere(['like', 'hasil', $this->hasil])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
