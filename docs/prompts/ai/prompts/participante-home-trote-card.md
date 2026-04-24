# Nova tarefa — Módulo Participante

## Contexto

URL:
[http://localhost:8080/participante/default/home](http://localhost:8080/participante/default/home)

---

## Regra de negócio

Sempre que um acadêmico autenticado estiver na home **e ainda não estiver participando do trote ativo**, o sistema deve exibir um card para iniciar sua participação no trote ativo.

---

## Conteúdo do card

**Texto:**

> clique abaixo e comece a participar do trote solidário 2026/1

**Ação:**
[participar agora]

---

## Modo de execução obrigatório

A tarefa deve ser executada em **duas etapas obrigatórias**.

---

# ETAPA 1 — SOMENTE ANÁLISE

Antes de qualquer implementação:

* ❌ Não alterar arquivos
* ❌ Não escrever código final
* ❌ Não implementar nada

### Objetivo

Realizar uma análise completa da aplicação e apresentar um plano de implementação respeitando:

* arquitetura existente
* padrões do projeto
* boas práticas adotadas

---

## Pontos que devem ser identificados

* onde a home do participante é carregada
* qual controller, action, serviço, caso de uso, presenter, helper ou componente participa do fluxo
* como o usuário autenticado é obtido
* como o sistema identifica que ele é um acadêmico
* como o trote ativo é obtido
* como a participação do acadêmico no trote ativo é verificada
* melhor ponto para decidir a exibição do card
* melhor estrutura/componente para renderizar o card
* rota, action ou fluxo para o botão **“participar agora”**
* elementos reaproveitáveis da aplicação

---

## Também deve apresentar

* plano funcional
* plano técnico
* fluxo completo da funcionalidade
* arquivos que serão alterados
* arquivos que serão criados (se necessário)
* riscos, dependências e cuidados
* critérios de aceite

---

## Final da ETAPA 1

Escrever exatamente:

```
AGUARDANDO APROVAÇÃO PARA IMPLEMENTAR
```

---

# ETAPA 2 — IMPLEMENTAÇÃO

Somente após aprovação.

---

## Requisitos da implementação

* seguir a arquitetura existente
* seguir padrões já adotados
* evitar duplicação de lógica
* reaproveitar estruturas existentes
* manter responsabilidades bem definidas
* preservar padrão visual e funcional

---

## Entregas após implementação

* resumo da solução aplicada
* arquivos criados
* arquivos alterados
* explicação objetiva de cada alteração
* fluxo final da funcionalidade
* trechos principais do código
* observações relevantes

---

# Regras de negócio consolidadas

## Exibir o card somente quando:

* usuário estiver autenticado
* usuário for um acadêmico
* estiver na home do participante
* existir um trote ativo
* acadêmico **não estiver participando** do trote ativo

---

## Não exibir o card quando:

* não houver usuário autenticado
* usuário não for acadêmico
* não existir trote ativo
* acadêmico já estiver participando

---

# Diretrizes obrigatórias

* não inventar nova arquitetura
* não fazer refatorações desnecessárias
* não alterar mais do que o necessário
* não criar abstrações sem justificativa
* não colocar regra de negócio na view (se contrariar padrão atual)
* reutilizar fluxos existentes sempre que possível
* escolher a solução mais aderente ao sistema atual
* justificar alterações de arquivos

---

# Formato obrigatório da resposta (ETAPA 1)

1. Entendimento da demanda
2. Regra de negócio consolidada
3. Análise da implementação atual
4. Plano funcional
5. Plano técnico
6. Fluxo detalhado
7. Arquivos que serão criados
8. Arquivos que serão alterados
9. Riscos / pontos de atenção
10. Critérios de aceite
11. Aguardando aprovação
