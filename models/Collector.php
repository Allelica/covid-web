<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "collector".
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $name
 * @property string|null $description
 *
 * @property JoinUserCollector[] $joinUserCollectors
 * @property User[] $users
 */
class Collector extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'collector';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['code'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 300],
            [['description'], 'string', 'max' => 100],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * Gets query for [[JoinUserCollectors]].
     *
     * @return \yii\db\ActiveQuery|JoinUserCollectorQuery
     */
    public function getJoinUserCollectors()
    {
        return $this->hasMany(JoinUserCollector::className(), ['collector_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('join_user_collector', ['collector_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return CollectorQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CollectorQuery(get_called_class());
    }

    /**
     * Find the collector by code
     * @param String code
     * @return Collector
     */
     public static function findByCode($code) {
       return self::find()->where(['code' => $code])->one();
     }
}
