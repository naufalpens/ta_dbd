<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "kecamatan".
 *
 * @property integer $id
 * @property string $nama_kecamatan
 * @property string $koordinat
 */
class Kecamatan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kecamatan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nama_kecamatan', 'koordinat'], 'required'],
            [['koordinat'], 'string'],
            [['nama_kecamatan'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nama_kecamatan' => 'Nama Kecamatan',
            'koordinat' => 'Koordinat',
        ];
    }
}
