<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "prediksi_kecamatan".
 *
 * @property integer $id
 * @property integer $id_kecamatan
 * @property string $hasil
 * @property string $status
 */
class PrediksiKecamatan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'prediksi_kecamatan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_kecamatan', 'hasil', 'status'], 'required'],
            [['id_kecamatan'], 'integer'],
            [['hasil'], 'string', 'max' => 50],
            [['status'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_kecamatan' => 'Id Kecamatan',
            'hasil' => 'Hasil',
            'status' => 'Status',
        ];
    }
}
