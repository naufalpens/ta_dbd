<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "prediksi_kecamatan".
 *
 * @property integer $id
 * @property integer $id_kecamatan
 * @property integer $kasus
 * @property integer $prediksi
 * @property integer $error
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
            [['id_kecamatan', 'kasus', 'prediksi', 'error', 'status'], 'required'],
            [['id_kecamatan', 'kasus', 'prediksi', 'error'], 'integer'],
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
            'kasus' => 'Kasus',
            'prediksi' => 'Prediksi',
            'error' => 'Error',
            'status' => 'Status',
        ];
    }
}
