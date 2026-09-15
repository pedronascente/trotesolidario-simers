<?php

$this->title = 'Editar custo';
$this->params['breadcrumbs'][] = ['label' => 'Gestão de custos', 'url' => ['index', 'trote_id' => $model->trote_id]];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('_form', compact('model', 'trotes', 'universidades', 'tiposCategoria', 'distribuicoes'));
