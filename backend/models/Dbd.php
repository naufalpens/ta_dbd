<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "dbd".
 *
 * @property integer $id
 * @property string $tanggal
 * @property string $kecamatan
 * @property integer $ch
 * @property integer $hh
 * @property integer $abj
 * @property integer $hi
 * @property integer $kasus
 */
class Dbd extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dbd';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tanggal', 'kecamatan', 'ch', 'hh', 'abj', 'hi', 'kasus'], 'required'],
            [['tanggal'], 'safe'],
            [['ch', 'hh', 'abj', 'hi', 'kasus'], 'integer'],
            [['kecamatan'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tanggal' => 'Tanggal',
            'kecamatan' => 'Kecamatan',
            'ch' => 'Ch',
            'hh' => 'Hh',
            'abj' => 'Abj',
            'hi' => 'Hi',
            'kasus' => 'Kasus',
        ];
    }
}
