<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lang".
 *
 * @property int $id
 * @property string $description
 * @property string $icon
 * @property string $code
 *
 * @property DataCodingText[] $dataCodingTexts
 * @property Label[] $label
 * @property QuestionText[] $questionTexts
 */
class Lang extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lang';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'icon','label','code'], 'required'],
            [['icon'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 20],
            [['code'], 'string', 'max' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'description' => Yii::t('app', 'Description'),
            'icon' => Yii::t('app', 'Icon'),
            'code' => Yii::t('app', 'Code'),
        ];
    }

    /**
     * Gets query for [[DataCodingTexts]].
     *
     * @return \yii\db\ActiveQuery|DataCodingTextQuery
     */
    public function getDataCodingTexts()
    {
        return $this->hasMany(DataCodingText::className(), ['language_id' => 'id']);
    }

    /**
     * Gets query for [[Label]].
     *
     * @return \yii\db\ActiveQuery|LabelQuery
     */
    public function getLabel()
    {
        return $this->hasMany(Label::className(), ['id_language' => 'id']);
    }

    /**
     * Gets query for [[QuestionTexts]].
     *
     * @return \yii\db\ActiveQuery|QuestionTextQuery
     */
    public function getQuestionTexts()
    {
        return $this->hasMany(QuestionText::className(), ['language_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return LangQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LangQuery(get_called_class());
    }

    /**
     * Find language by language code
     * @param $code String
     * @return Lang
     * @TODO for performance it could be used the id also as key (?)
     */
     public static function findByCode($code) {
       $lang =  self::find()->where(['code'=>$code])->one();
       return $lang;
     }

     /**
      * Get the list of all configured languages
      * @return Lang[]
      * @TODO for performance it could be used the id also as key (?)
      */
      public static function getList() {
        $lang =  self::find()->all();
        return $lang;
      }
}
