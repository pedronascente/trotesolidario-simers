<?php

namespace app\modules\common\models;

use Yii;
use app\models\User;
use yii\db\ActiveRecord;

class Certificado extends ActiveRecord
{
    public static function tableName()
    {
        return 'certificado';
    }

    public function rules()
    {
        return [
            [['participacao_id', 'codigo_validador', 'carga_horaria_total', 'data_emissao', 'hash_integridade', 'emitido_por'], 'required'],
            [['participacao_id', 'carga_horaria_total', 'emitido_por'], 'integer'],
            [['data_emissao'], 'safe'],
            [['codigo_validador'], 'string', 'max' => 50],
            [['arquivo_pdf', 'hash_integridade'], 'string', 'max' => 255],
            [['codigo_validador'], 'unique'],
            [['participacao_id'], 'unique'],
            [['participacao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Participacao::class, 'targetAttribute' => ['participacao_id' => 'id']],
            [['emitido_por'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['emitido_por' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'participacao_id' => 'Participacao',
            'codigo_validador' => 'Codigo validador',
            'carga_horaria_total' => 'Carga horaria total',
            'arquivo_pdf' => 'Arquivo PDF',
            'data_emissao' => 'Data de emissao',
            'hash_integridade' => 'Hash de integridade',
            'emitido_por' => 'Emitido por',
        ];
    }

    public function getParticipacao()
    {
        return $this->hasOne(Participacao::class, ['id' => 'participacao_id']);
    }

    public function getEmissor()
    {
        return $this->hasOne(User::class, ['id' => 'emitido_por']);
    }

    public function getParticipanteNome(): string
    {
        return $this->participacao->user->nome ?? '-';
    }

    public function getParticipanteEmail(): string
    {
        return $this->participacao->user->email ?? '-';
    }

    public function getTroteDescricao(): string
    {
        $trote = $this->participacao->trote ?? null;
        if ($trote === null) {
            return '-';
        }

        $titulo = $trote->titulo ?: 'Sem titulo';
        $edicao = $trote->edicao ?: '-';

        return $titulo . ' | ' . $edicao;
    }

    public function getEventoNomes(): string
    {
        $trote = $this->participacao->trote ?? null;
        if ($trote === null || empty($trote->eventos)) {
            return '-';
        }

        $nomes = [];
        foreach ($trote->eventos as $evento) {
            $nomes[] = $evento->nome;
        }

        return empty($nomes) ? '-' : implode(', ', array_unique($nomes));
    }

    public function getArquivoPdfPath(): ?string
    {
        if (empty($this->arquivo_pdf)) {
            return null;
        }

        $relativePath = ltrim($this->arquivo_pdf, '/');
        $candidates = [];

        foreach (['@webroot', '@app/web'] as $alias) {
            $basePath = Yii::getAlias($alias, false);
            if (!is_string($basePath) || $basePath === '') {
                continue;
            }

            $candidates[] = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
        }

        foreach (array_unique($candidates) as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }
}


