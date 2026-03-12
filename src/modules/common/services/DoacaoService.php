<?php

namespace app\modules\common\services;

use Yii;
use yii\web\UploadedFile;
use app\modules\common\models\Doacao;
use app\modules\common\services\contracts\DoacaoServiceInterface;

class DoacaoService implements DoacaoServiceInterface
{
    public function create(Doacao $doacao): bool
    {
        return Yii::$app->db->transaction(function() use ($doacao){    
            $arquivo = UploadedFile::getInstance($doacao, 'file');
            if($arquivo)
            {
                $doacao->arquivo = $this->saveArquivo($arquivo);
            }

            if (!$doacao->save()) 
            {
                return false;
            }
            return true;
        });    
    }

    protected function saveArquivo(UploadedFile $file): string
    {
        $dir = Yii::getAlias('@imgArquivosDoacao');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $nome = uniqid('uni_') . '.' . $file->extension;
        $file->saveAs($dir . $nome);

        return $nome;
    }

    protected function removeArquivo(?string $arquivo): void
    {
        if ($arquivo) {
            $path = Yii::getAlias('@webroot/img/') . $arquivo;
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }















    public function update(Doacao $doacao): bool
    {
        return $doacao->save();
    }

    public function aprovar(Doacao $doacao, int $adminId): bool
    {
        $doacao->status = Doacao::STATUS_APROVADO;
        $doacao->validado_por = $adminId;
        $doacao->validado_em = date('Y-m-d H:i:s');

        return $doacao->save(false);
    }

    public function rejeitar(Doacao $doacao, int $adminId): bool
    {
        $doacao->status = Doacao::STATUS_REJEITADO;
        $doacao->validado_por = $adminId;
        $doacao->validado_em = date('Y-m-d H:i:s');

        return $doacao->save(false);
    }
}
