# 🚀 Nova tarefa --- Redesign UX/UI Dashboard (Módulo Participante)

## 📍 Contexto

URL: http://localhost:8080/participante/default/home

Tela atual: Dashboard do participante (Trote Solidário)

## 🎯 Objetivo

Criar um plano completo de UX e UI para modernizar o dashboard do participante,
tornando a página mais intuitiva, organizada, responsiva e orientada à ação,
utilizando PHP, Yii2, Bootstrap 4.5.3, CSS e JavaScript.

⚠️ IMPORTANTE: NÃO implementar ainda, apenas planejar.

## 📋 Escopo da melhoria

Analisar e propor melhorias para:

- Header / Minha área
- Seção "Trote ativo"
- Cards de resumo (participações, doações, certificados)
- Minhas participações
- Ranking por universidade
- Informativos
- Regulamentos
- Bloco de doação de alimentos (call-to-action principal)
- Botões e navegação lateral
- Hierarquia visual, espaçamentos e responsividade

## ⚙️ Diretrizes técnicas

- Manter Yii2 como base
- Utilizar Bootstrap 4.5.3 (grid, componentes, responsividade)
- Melhorar CSS (organização, padronização visual)
- Usar JavaScript apenas quando necessário (UX/interações)
- NÃO duplicar código existente
- Priorizar reaproveitamento
- Respeitar limitações e padrões do Bootstrap 4.5.3 (grid 12 colunas, utilities, cards, flex)

## 🔎 ETAPA 1 --- ANÁLISE

- Analisar a interface atual do dashboard
- Identificar problemas de UX (confusão, excesso, baixa prioridade visual)
- Identificar problemas de UI (cores, tipografia, espaçamento, consistência)
- Avaliar responsividade (baseada em Bootstrap 4.5.3)
- Mapear estrutura atual (views, layouts, widgets, partials)

- Propor:
  - Nova hierarquia de informações
  - Nova organização dos blocos
  - Melhorias visuais (UI moderna com Bootstrap 4.5.3)
  - Melhor experiência do usuário (UX)
  - Destaque para ações principais (ex: doar)

- Criar um plano técnico considerando:
  - Yii2 (views, layouts, components)
  - Bootstrap 4.5.3 (grid system, cards, utilities, spacing)
  - CSS (refatoração visual)
  - JS (interações leves)

- Listar arquivos que serão alterados:
  - views
  - layouts
  - partials
  - assets (css/js)

- Para cada arquivo:
  - Explicar o que será modificado
  - Justificar a alteração

- Definir ordem segura de implementação

- Identificar riscos:
  - quebra de layout
  - impacto em outras telas
  - dependências de dados
  - limitações do Bootstrap 4.5.3

Finalizar com:
AGUARDANDO APROVAÇÃO PARA IMPLEMENTAR

## 🛠️ ETAPA 2 --- IMPLEMENTAÇÃO

(SOMENTE após aprovação)

- Aplicar melhorias seguindo o plano
- Reutilizar código existente
- Não duplicar lógica
- Manter padrões do projeto Yii2
- Garantir responsividade com Bootstrap 4.5.3

## 🧪 Testes (Docker)

Validar:

- Renderização correta do dashboard
- Responsividade (desktop, tablet, mobile)
- Funcionamento dos botões e links
- Exibição correta dos dados (participações, ranking, etc)
- Performance da página

## 📦 Entregáveis

- Lista de arquivos criados/alterados (com paths)
- Explicação das mudanças realizadas
- Descrição da nova UX/UI
- Antes vs depois (conceitual)
- Evidências de testes via Docker