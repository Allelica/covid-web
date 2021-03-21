<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "label".
 *
 * @property int $id
 * @property string $label
 * @property string $text
 * @property int $id_language
 *
 * @property Lang $language
 */
class Label extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'label';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['label', 'text', 'id_language'], 'required'],
            [['text'], 'string'],
            [['id_language'], 'integer'],
            [['label'], 'string', 'max' => 50],
            [['id_language'], 'unique'],
            [['id_language'], 'exist', 'skipOnError' => true, 'targetClass' => Lang::className(), 'targetAttribute' => ['id_language' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'label' => Yii::t('app', 'Label'),
            'text' => Yii::t('app', 'Text'),
            'id_language' => Yii::t('app', 'Id Language'),
        ];
    }

    /**
     * Gets query for [[Language]].
     *
     * @return \yii\db\ActiveQuery|LangQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Lang::className(), ['id' => 'id_language']);
    }

    /**
     * {@inheritdoc}
     * @return LabelQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LabelQuery(get_called_class());
    }

    /**
     * Get the list of labels for the setted language
     */
     public static function getList() {
       $lang = Yii::$app->params['language'];
       $labels = self::find()->where(['id_language'=>$lang])->all();
       $ret = [];
       foreach($labels as $label) {
         $ret[$label->label] = $label->text;
       }
       return $ret;
     }
}
