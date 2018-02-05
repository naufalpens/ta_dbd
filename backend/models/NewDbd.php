<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "new_dbd".
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
class NewDbd extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'new_dbd';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tanggal', 'kecamatan', 'ch', 'hh', 'abj', 'hi', 'kasus'], 'required'],
            [['id', 'ch', 'hh', 'abj', 'hi', 'kasus'], 'integer'],
            [['tanggal'], 'safe'],
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
