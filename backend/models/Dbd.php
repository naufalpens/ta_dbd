<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "dbd".
 *
 * @property integer $id
 * @property string $tanggal
 * @property integer $id_kecamatan
 * @property integer $ch
 * @property integer $hh
 * @property integer $abj
 * @property integer $hi
 * @property integer $kasus
 *
 * @property Kecamatan $idKecamatan
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
            [['tanggal', 'id_kecamatan', 'ch', 'hh', 'abj', 'hi', 'kasus'], 'required'],
            [['tanggal'], 'safe'],
            [['id_kecamatan', 'ch', 'hh', 'abj', 'hi', 'kasus'], 'integer'],
            [['id_kecamatan'], 'exist', 'skipOnError' => true, 'targetClass' => Kecamatan::className(), 'targetAttribute' => ['id_kecamatan' => 'id']],
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
            'id_kecamatan' => 'Id Kecamatan',
            'ch' => 'Ch',
            'hh' => 'Hh',
            'abj' => 'Abj',
            'hi' => 'Hi',
            'kasus' => 'Kasus',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdKecamatan()
    {
        return $this->hasOne(Kecamatan::className(), ['id' => 'id_kecamatan']);
    }
}
