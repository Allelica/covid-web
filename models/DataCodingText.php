<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "data_coding_text".
 *
 * @property int $id
 * @property int $data_coding_id
 * @property int $language_id
 * @property string $title
 * @property string $subtitle
 * @property string $info
 *
 * @property DataCoding $dataCoding
 * @property Lang $language
 */
class DataCodingText extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'data_coding_text';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data_coding_id', 'language_id', 'text'], 'required'],
            [['data_coding_id', 'language_id'], 'integer'],
            [['title','subtitle','info'], 'string'],
            [['data_coding_id'], 'exist', 'skipOnError' => true, 'targetClass' => DataCoding::className(), 'targetAttribute' => ['data_coding_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lang::className(), 'targetAttribute' => ['language_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'data_coding_id' => Yii::t('app', 'Data Coding ID'),
            'language_id' => Yii::t('app', 'Language ID'),
            'title' => Yii::t('app', 'Title'),
            'subtitle' => Yii::t('app', 'Subtitle'),
            'info' => Yii::t('app', 'Info'),
        ];
    }

    /**
     * Gets query for [[DataCoding]].
     *
     * @return \yii\db\ActiveQuery|DataCodingQuery
     */
    public function getDataCoding()
    {
        return $this->hasOne(DataCoding::className(), ['id' => 'data_coding_id']);
    }



    /**
     * Gets query for [[Language]].
     *
     * @return \yii\db\ActiveQuery|LangQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Lang::className(), ['id' => 'language_id']);
    }

    /**
     * {@inheritdoc}
     * @return DataCodingTextQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DataCodingTextQuery(get_called_class());
    }
}
