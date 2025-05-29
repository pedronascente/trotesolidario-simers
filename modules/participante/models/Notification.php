<?php

namespace app\modules\participante\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "notification".
 *
 * @property int $id
 * @property int $user_id
 * @property string $message
 * @property string $type
 * @property int $read
 * @property string $created_at
 */
class Notification extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'message', 'type'], 'required'],
            [['user_id', 'read'], 'integer'],
            [['message'], 'string'],
            [['created_at'], 'safe'],
            [['type'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Usuário',
            'message' => 'Mensagem',
            'type' => 'Tipo',
            'read' => 'Lido',
            'created_at' => 'Data de Criação',
        ];
    }

    /**
     * Cria uma nova notificação para o usuário
     */
    public static function create($userId, $message, $type = 'info')
    {
        $notification = new self();
        $notification->user_id = $userId;
        $notification->message = $message;
        $notification->type = $type;
        $notification->read = 0;
        $notification->created_at = date('Y-m-d H:i:s');
        return $notification->save();
    }

    /**
     * Retorna notificações não lidas do usuário
     */
    public static function getUnread($userId)
    {
        return self::find()
            ->where(['user_id' => $userId, 'read' => 0])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
    }
} 