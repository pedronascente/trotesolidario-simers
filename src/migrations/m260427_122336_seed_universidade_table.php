<?php

use yii\db\Migration;

class m260427_122336_seed_universidade_table extends Migration
{
    public function safeUp()
    {
        $now = date('Y-m-d H:i:s');

        $this->batchInsert('{{%universidade}}', [
            'nome',
            'cidade',
            'uf',
            'icon',
            'link_doacao_alimento',
            'ativo',
            'created_at',
            'updated_at'
        ], [
            //['UFRGS - Universidade Federal do RS', 'Porto Alegre', 'RS', null, 'https://bit.ly/ufrgs26', true, $now, $now],
            // ['UFCSPA - Universidade Federal de Ciências da Saúde de POA', 'Porto Alegre', 'RS', null, 'https://bit.ly/ufcspa26', true, $now, $now],
            // ['PUCRS - Pontifícia Universidade Católica do RGS', 'Porto Alegre', 'RS', null, 'https://bit.ly/pucrs26', true, $now, $now],
            //['UNISINOS - Universidade do Vale do Rio dos Sinos', 'São Leopoldo', 'RS', null, 'https://bit.ly/unisinos26', true, $now, $now],
            //  ['FEEVALE', 'Novo Hamburgo', 'RS', null, 'https://bit.ly/feevale26', true, $now, $now],
            ['UCS - Universidade Caxias do Sul', 'Caxias do Sul', 'RS', null, 'https://bit.ly/ucs26', true, $now, $now],
            ['UPF - Universidade de Passo Fundo', 'Passo Fundo', 'RS', null, 'https://bit.ly/upf2026', true, $now, $now],
            ['ATITUS', 'Passo Fundo', 'RS', null, 'https://bit.ly/atitus26', true, $now, $now],
            ['UFFS - Universidade Federal da Fronteira do Sul', 'Erechim', 'RS', null, 'https://bit.ly/uffs26', true, $now, $now],
            ['UCPEL - Universidade Católica de Pelotas', 'Pelotas', 'RS', null, 'https://bit.ly/ucpel26', true, $now, $now],
            ['UFPEL - Universidade Federal de Pelotas', 'Pelotas', 'RS', null, 'https://bit.ly/ufpel26', true, $now, $now],
            ['FURG - Universidade Federal de Rio Grande', 'Rio Grande', 'RS', null, 'https://bit.ly/furg26', true, $now, $now],
            ['UNISC - Universidade de Santa Cruz', 'Santa Cruz do Sul', 'RS', null, 'https://bit.ly/unisc26', true, $now, $now],
            ['UFSM - Universidade Federal Santa Maria', 'Santa Maria', 'RS', null, 'https://bit.ly/ufsm26', true, $now, $now],
            ['UFN - Universidade Franciscana', 'Santa Maria', 'RS', null, 'https://bit.ly/ufn26', true, $now, $now],
            ['UNIVATES - Fundação Vale do Taquari', 'Lajeado', 'RS', null, 'https://bit.ly/univates26', true, $now, $now],
            ['UNIPAMPA - Universidade Federal do Pampa', 'Bagé', 'RS', null, 'https://bit.ly/unipampa26', true, $now, $now],
            ['URI - Universidade Regional Integrada do Alto Uruguai e das Missões', 'Erechim', 'RS', null, 'https://bit.ly/uri2026', true, $now, $now],
            ['UNIJUÍ - Universidade Regional do Noroeste do Estado do Rio Grande do Sul', 'Ijuí', 'RS', null, 'https://bit.ly/unijui26', true, $now, $now],
        ]);
    }

    public function safeDown()
    {
        $this->delete('{{%universidade}}', [
            'link_doacao_alimento' => [
               // 'https://bit.ly/ufrgs26',
               // 'https://bit.ly/ufcspa26',
               // 'https://bit.ly/pucrs26',
               // 'https://bit.ly/unisinos26',
               // 'https://bit.ly/feevale26',
                'https://bit.ly/ucs26',
                'https://bit.ly/upf2026',
                'https://bit.ly/atitus26',
                'https://bit.ly/uffs26',
                'https://bit.ly/ucpel26',
                'https://bit.ly/ufpel26',
                'https://bit.ly/furg26',
                'https://bit.ly/unisc26',
                'https://bit.ly/ufsm26',
                'https://bit.ly/ufn26',
                'https://bit.ly/univates26',
                'https://bit.ly/unipampa26',
                'https://bit.ly/uri2026',
                'https://bit.ly/unijui26',
            ]
        ]);
    }
}