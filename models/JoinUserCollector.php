<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "join_user_collector".
 *
 * @property int $user_id
 * @property int $collector_id
 * @property string|null $code Sample code provided by collector
 *
 * @property User $user
 * @property Collector $collector
 */
class JoinUserCollector extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'join_user_collector';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'collector_id'], 'required'],
            [['user_id', 'collector_id'], 'integer'],
            [['code'], 'string', 'max' => 256],
            [['user_id', 'collector_id'], 'unique', 'targetAttribute' => ['user_id', 'collector_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['collector_id'], 'exist', 'skipOnError' => true, 'targetClass' => Collector::className(), 'targetAttribute' => ['collector_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'User ID'),
            'collector_id' => Yii::t('app', 'Collector ID'),
            'code' => Yii::t('app', 'Sample code provided by collector'),
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Collector]].
     *
     * @return \yii\db\ActiveQuery|CollectorQuery
     */
    public function getCollector()
    {
        return $this->hasOne(Collector::className(), ['id' => 'collector_id']);
    }

    /**
     * {@inheritdoc}
     * @return JoinUserCollectorQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new JoinUserCollectorQuery(get_called_class());
    }
}
