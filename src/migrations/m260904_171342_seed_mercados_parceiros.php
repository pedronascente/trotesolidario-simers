<?php

use yii\db\Migration;

class m260904_171342_seed_mercados_parceiros extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
          $mercados = [
            [
                'nome_mercado' => 'Mercado Central Sul',
                'endereco' => 'Av. Bento Gonçalves',
                'numero' => '1250',
                'bairro' => 'Partenon',
            ],
            [
                'nome_mercado' => 'Supermercado Boa Compra',
                'endereco' => 'Rua das Flores',
                'numero' => '845',
                'bairro' => 'Centro',
            ],
            [
                'nome_mercado' => 'Mercado São Lucas',
                'endereco' => 'Av. Dorival Cândido Luz de Oliveira',
                'numero' => '2150',
                'bairro' => 'Centro',
            ],
            [
                'nome_mercado' => 'Supermercado Econômico',
                'endereco' => 'Rua Bento Martins',
                'numero' => '432',
                'bairro' => 'Santa Isabel',
            ],
            [
                'nome_mercado' => 'Mercado Família',
                'endereco' => 'Av. Pedro Adams Filho',
                'numero' => '1780',
                'bairro' => 'Centro',
            ],
            [
                'nome_mercado' => 'Supermercado Avenida',
                'endereco' => 'Rua Independência',
                'numero' => '965',
                'bairro' => 'Centro',
            ],
            [
                'nome_mercado' => 'Mercado Popular',
                'endereco' => 'Av. Júlio de Castilhos',
                'numero' => '2345',
                'bairro' => 'Centro',
            ],
            [
                'nome_mercado' => 'Supermercado Pampa',
                'endereco' => 'Rua General Osório',
                'numero' => '710',
                'bairro' => 'Centro',
            ],
            [
                'nome_mercado' => 'Mercado do Bairro',
                'endereco' => 'Av. Presidente Vargas',
                'numero' => '1560',
                'bairro' => 'Cidade Nova',
            ],
            [
                'nome_mercado' => 'Supermercado União',
                'endereco' => 'Rua do Acampamento',
                'numero' => '320',
                'bairro' => 'Centro',
            ],
            [
                'nome_mercado' => 'Mercado Gaúcho',
                'endereco' => 'Av. Brasil',
                'numero' => '1890',
                'bairro' => 'Centro',
            ],
            [
                'nome_mercado' => 'Supermercado Mais Brasil',
                'endereco' => 'Rua Marechal Deodoro',
                'numero' => '1425',
                'bairro' => 'Centro',
            ],
            [
                'nome_mercado' => 'Mercado Paraná',
                'endereco' => 'Av. Higienópolis',
                'numero' => '875',
                'bairro' => 'Centro',
            ],
            [
                'nome_mercado' => 'Supermercado Bom Vizinho',
                'endereco' => 'Rua Felipe Schmidt',
                'numero' => '620',
                'bairro' => 'Centro',
            ],
            [
                'nome_mercado' => 'Mercado Ilha Sul',
                'endereco' => 'Av. Leoberto Leal',
                'numero' => '1180',
                'bairro' => 'Barreiros',
            ],
            [
                'nome_mercado' => 'Supermercado Nova Era',
                'endereco' => 'Rua Augusta',
                'numero' => '1530',
                'bairro' => 'Consolação',
            ],
            [
                'nome_mercado' => 'Mercado Paulista',
                'endereco' => 'Av. Andrade Neves',
                'numero' => '920',
                'bairro' => 'Centro',
            ],
            [
                'nome_mercado' => 'Supermercado Esperança',
                'endereco' => 'Av. Amazonas',
                'numero' => '2450',
                'bairro' => 'Santo Agostinho',
            ],
            [
                'nome_mercado' => 'Mercado Mineirão',
                'endereco' => 'Av. Floriano Peixoto',
                'numero' => '1325',
                'bairro' => 'Centro',
            ],
            [
                'nome_mercado' => 'Supermercado Nordeste',
                'endereco' => 'Av. Norte Miguel Arraes',
                'numero' => '1850',
                'bairro' => 'Casa Amarela',
            ],
        ];

        $this->batchInsert(
            '{{%mercado_parceiro}}',
            [
                'nome_mercado',
                'endereco',
                'numero',
                'bairro'
            ], $mercados
        );
    }

    public function safeDown()
    {
        $this->delete('{{%mercado_parceiro}}', [
            'nome_mercado' => [
                'Mercado Central Sul',
                'Supermercado Boa Compra',
                'Mercado São Lucas',
                'Supermercado Econômico',
                'Mercado Família',
                'Supermercado Avenida',
                'Mercado Popular',
                'Supermercado Pampa',
                'Mercado do Bairro',
                'Supermercado União',
                'Mercado Gaúcho',
                'Supermercado Mais Brasil',
                'Mercado Paraná',
                'Supermercado Bom Vizinho',
                'Mercado Ilha Sul',
                'Supermercado Nova Era',
                'Mercado Paulista',
                'Supermercado Esperança',
                'Mercado Mineirão',
                'Supermercado Nordeste',
            ],
        ]);
    }
}
