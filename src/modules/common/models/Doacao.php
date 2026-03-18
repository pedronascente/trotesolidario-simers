<?php

namespace app\modules\common\models;

use app\modules\common\models\Evento;
use app\modules\common\models\TipoDoacao;
use app\modules\common\models\Trote;
use app\modules\common\models\Universidade;
use app\modules\common\models\Users;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Doacao extends ActiveRecord
{
    public $file;

    const STATUS_PENDENTE = 'pendente';
    const STATUS_APROVADO = 'aprovado';
    const STATUS_REJEITADO = 'rejeitado';

    public static function tableName()
    {
        return 'doacao';
    }

    public function rules()
    {
        return [
            [['trote_id', 'evento_id', 'user_id', 'universidade_id', 'tipo_doacao_id'], 'required'],
            //['tipo_doacao_id', 'validateDoacaoUnica'],

            [['trote_id', 'evento_id', 'user_id', 'tipo_doacao_id', 'validado_por'], 'integer'],
            [['validado_em', 'created_at', 'updated_at'], 'safe'],
            [['status'], 'in', 'range' => [
                self::STATUS_PENDENTE,
                self::STATUS_APROVADO,
                self::STATUS_REJEITADO
            ]],
            [['comprovante'], 'string', 'max' => 255],
            [['arquivo'], 'string', 'max' => 255],
            [
                ['file'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'maxSize' => 1024 * 1024 * 2,
                'mimeTypes' => 'image/*',
            ],
            // REGRA DE NEGÓCIO
            [
                ['user_id'],
                'unique',
                'targetAttribute' => ['user_id', 'trote_id', 'tipo_doacao_id'],
                'message' => 'Este usuário já realizou este tipo de doação neste trote.'
            ],
            [
                ['observacao'],
                'required',
                'when' => function ($model) {
                    return $model->status === self::STATUS_REJEITADO;
                },
                'whenClient' => "function (attribute, value) {
                    return $('#doacao-status').val() === 'rejeitado';
                }"
            ],
            [['status'], 'default', 'value' => self::STATUS_PENDENTE],
            [['observacao'], 'string', 'max' => 1000],
        ];
    }

    public function attributeLabels()
    {
        return [
            'trote_id'        => 'Trote',
            'evento_id'       => 'Evento',
            'user_id'         => 'Usuario',
            'universidade_id' => 'Universidade',
            'tipo_doacao_id'  => 'Tipo Doação',
            'arquivo'         => 'Imagem',
            'file'            => 'Imagem',
            'observacao'      => 'Motivo Reprovação',
            'trote.titulo'    => 'Trote',
            'user.name'       => 'Usuario',
            'tipoDoacao.nome' => 'Tipo Doação',
        ];
    }

    public function validateDoacaoUnica($attribute)
    {
        $existe = self::find()
            ->where([
                'user_id' => $this->user_id,
                'trote_id' => $this->trote_id,
                'tipo_doacao_id' => $this->tipo_doacao_id
            ])
            ->andWhere(['!=', 'id', $this->id])
            ->exists();

        if ($existe) {
            $this->addError($attribute, 'Este usuário já realizou este tipo de doação neste trote.');
        }
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

    // ================= RELAÇÕES =================

    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

    public function getTrote()
    {
        return $this->hasOne(Trote::class, ['id' => 'trote_id']);
    }

    public function getEvento()
    {
        return $this->hasOne(Evento::class, ['id' => 'evento_id']);
    }

    public function getTipoDoacao()
    {
        return $this->hasOne(TipoDoacao::class, ['id' => 'tipo_doacao_id']);
    }

    public function getUniversidade()
    {
        return $this->hasOne(Universidade::class, ['id' => 'universidade_id']);
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_APROVADO => 'Aprovado',
            self::STATUS_REJEITADO => 'Rejeitado',
        ];
    }


    /*
    Ele retorna os tipos de doação que ainda estão disponíveis para um determinado usuário ($userId) dentro de um trote ($troteId).
    Ou seja: não lista tipos que o usuário já fez nesse trote, apenas os restantes ativos.
    */
    public static function getTiposDisponiveis($userId, $troteId)
    {
        $tiposJaFeitos = self::find()
            ->select('tipo_doacao_id')
            ->where([
                'user_id' => $userId,
                'trote_id' => $troteId
            ]);

        return TipoDoacao::find()
            ->where(['ativo' => 1])
            ->andWhere(['not in', 'id', $tiposJaFeitos])
            ->orderBy('nome')
            ->all();
    }
}
