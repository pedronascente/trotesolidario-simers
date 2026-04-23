---
name: agent-revisar-aplicacao-pos-tarefa
description: Use quando precisar de revisão pós-implementação após uma tarefa. Esse skill analisa bugs, riscos, regressões, lista arquivos alterados, propõe correções incrementais e aguarda aprovação antes de qualquer mudança.
---

# Agent Revisar Aplicacao Pos Tarefa

## Objetivo
Executar uma revisão técnica pós-tarefa para validar qualidade, detectar riscos e orientar correções seguras antes de novas alterações.

## Fluxo obrigatório
1. Analisar o que foi implementado
2. Diagnosticar erros, bugs e riscos de regressão
3. Listar arquivos alterados e impacto por arquivo
4. Propor correções incrementais (sem aplicar)
5. Aguardar aprovação explícita do usuário
6. Disponibilizar relatório final estruturado

## Regras
- Não editar arquivos automaticamente.
- Não aplicar refatorações sem aprovação.
- Priorizar alto impacto + baixo risco.
- Referenciar arquivo e linha sempre que possível.
- Ser objetivo, técnico e verificável.

## Checklist de revisão
- Funcionalidade: o comportamento solicitado foi entregue?
- Regressão: algo existente pode ter quebrado?
- Acessibilidade: foco, teclado, semântica, contraste
- Responsividade: mobile/tablet/desktop
- Código: legibilidade, duplicação, acoplamento, manutenção
- Performance: re-renders, peso desnecessário, operações em render
- UX/UI: escaneabilidade, hierarquia, clareza de CTA

## Formato do relatório
### Resumo executivo
- visão rápida do estado geral

### Achados por severidade
Para cada achado:
- Onde está (arquivo + linha)
- Por que é problema
- Severidade (Alta/Média/Baixa)
- Impacto esperado

### Arquivos alterados
- caminho
- finalidade da alteração

### Correções recomendadas
- lista priorizada (baixo risco primeiro)

### Plano de execução após aprovação
- passos curtos e incrementais

### Status
- sempre finalizar com: "Aguardando aprovação"

## Saída mínima esperada
- 1 diagnóstico claro
- 1 lista de arquivos alterados
- 1 plano de correções
- status final de aprovação pendente
