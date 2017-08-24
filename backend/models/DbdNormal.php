<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "dbd_normal".
 *
 * @property integer $id
 * @property string $tanggal
 * @property integer $id_kecamatan
 * @property double $ch
 * @property double $hh
 * @property double $abj
 * @property double $hi
 * @property double $kasus
 */
class DbdNormal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dbd_normal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tanggal', 'id_kecamatan', 'ch', 'hh', 'abj', 'hi', 'kasus'], 'required'],
            [['tanggal'], 'safe'],
            [['id_kecamatan'], 'integer'],
            [['ch', 'hh', 'abj', 'hi', 'kasus'], 'number'],
            [['kecamatan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kecamatan::className(), 'targetAttribute' => ['id_kecamatan' => 'id']],
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
    public function getKecamatan()
    {
        return $this->hasOne(Kecamatan::className(), ['id' => 'id_kecamatan']);
    }
}
