# 🧠 Biblioteca de Prompts — Projeto Trote Solidário

Este diretório contém os prompts utilizados para automação de tarefas com IA (Codex / ChatGPT), organizados por contexto funcional da aplicação.

O objetivo é:
- padronizar a criação de tarefas com IA
- reaproveitar prompts eficientes
- manter histórico e evolução dos prompts
- facilitar manutenção e entendimento do sistema

---

## 📁 Estrutura
/docs/prompts/ai/
README.md
/prompts/
participante-home-trote-card.md


---

## 📌 Convenção de nomes

Os arquivos devem seguir o padrão:
[modulo]-[contexto]-[acao].md


### Exemplos:

- `participante-home-trote-card.md`
- `auth-validacao-login.md`
- `financeiro-gerar-relatorio.md`

---

## 🧩 Estrutura recomendada de um prompt

Cada prompt deve conter:

```md
# Nome da tarefa

## Contexto
Descrição do cenário

## Regra de negócio
O que deve ser feito

## Execução
Como a IA deve trabalhar (ex: análise + implementação)

## Regras obrigatórias
Restrições importantes

## Formato de saída
Como a resposta deve ser estruturada