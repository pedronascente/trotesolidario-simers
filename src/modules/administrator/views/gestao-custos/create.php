<?php

$this->title = 'Novo custo';
$this->params['breadcrumbs'][] = ['label' => 'Gestão de custos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('_form', compact('model', 'trotes', 'universidades', 'tiposCategoria', 'distribuicoes'));
