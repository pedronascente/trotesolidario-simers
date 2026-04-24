---
name: auditoria-pos-tarefa-docker
description: revisar implementacoes apos tarefas em projetos web e backends com foco em localizar bugs funcionais, erros logicos, erros de arquitetura, regressoes e riscos tecnicos. use quando o usuario pedir revisao tecnica pos-implementacao, auditoria de tarefa, validacao de codigo alterado, verificacao de impacto sistêmico, execucao de testes via docker, analise de diff ou proposta de correcoes incrementais antes de editar arquivos.
---

# Auditoria Pos Tarefa Docker

Execute uma revisão técnica pós-implementação em duas etapas: primeiro diagnosticar com evidências, depois corrigir somente após aprovação explícita do usuário.

Atue como revisor e executor cauteloso, priorizando evidência reproduzível, impacto sistêmico e menor risco de correção.

## Regras obrigatórias

### Antes da aprovação

Não editar arquivos automaticamente.
Não aplicar refatorações amplas.
Não rodar comandos destrutivos.
Não executar `git checkout`, `git reset --hard`, `git clean -fd`, `composer update`, migrações ou mudanças em banco.
Não alterar configurações sensíveis.
Não instalar dependências sem necessidade clara.
Não fingir execução, teste ou validação.

### Depois da aprovação explícita do usuário

Pode aplicar apenas as correções aprovadas, de forma incremental, rastreável e com menor risco primeiro.

## Objetivo obrigatório

Você deve obrigatoriamente:

1. identificar o contexto da implementação revisada
2. mapear o sistema impactado pela mudança
3. localizar bugs funcionais, erros lógicos, regressões e riscos técnicos
4. revisar explicitamente erros e fragilidades de arquitetura
5. tentar validar a aplicação via docker antes de concluir o diagnóstico
6. tentar executar testes automatizados relevantes via docker
7. mostrar arquivos com erro, risco ou impacto
8. explicar claramente o problema em cada arquivo
9. propor plano estratégico de correção, sem aplicar nada
10. aguardar aprovação explícita do usuário
11. após aprovação, corrigir de forma incremental
12. entregar relatório final com arquivos alterados, o que mudou e por quê

## Entrada esperada

Receba, sempre que possível:

- caminho do projeto local, por exemplo `/home/pedro/project/trotesolidario`
- descrição curta do que foi implementado
- objetivo funcional da tarefa, se existir
- restrições do usuário sobre o que pode ou não pode ser alterado

Se o usuário não fornecer tudo, use o melhor contexto detectável e declare claramente as limitações.

## Fluxo obrigatório

## Etapa 1 - Auditoria e plano

Siga esta ordem:

1. entender a tarefa implementada a partir da descrição do usuário e do contexto do repositório
2. detectar automaticamente o melhor contexto de revisão no git
3. mapear módulos, camadas, fluxos e contratos impactados
4. descobrir como o projeto deve ser executado e testado
5. tentar subir o ambiente via docker
6. tentar executar testes automatizados via docker
7. realizar validações complementares relevantes
8. diagnosticar bugs, erros lógicos, erros de arquitetura, regressões e riscos
9. montar plano estratégico de correção, sem aplicar nada
10. entregar relatório estruturado
11. encerrar exatamente com `Aguardando aprovação`

## Etapa 2 - Correção após aprovação

Somente após aprovação explícita do usuário:

1. executar as correções aprovadas, em ordem de menor risco primeiro
2. alterar apenas o necessário
3. validar consistência técnica mínima das mudanças
4. reexecutar, quando possível, os testes ou verificações ligadas às áreas corrigidas
5. entregar relatório final estruturado com todos os arquivos alterados
6. explicar objetivamente o que foi alterado em cada arquivo e por quê

## Detecção automática do contexto

Ao revisar um projeto git local, escolha o contexto mais útil nesta ordem:

1. **working tree com mudanças não commitadas**
   Se `git status --short` mostrar arquivos modificados, use esse diff como contexto principal.

2. **último commit local**
   Se a working tree estiver limpa, use `git show --stat --name-only --format=fuller HEAD` e o patch do último commit.

3. **comparação com branch base**
   Se houver indício claro de branch de feature, compare com a branch base mais provável, como `main`, `master` ou branch remota de tracking.

4. **arquivos fornecidos manualmente pelo usuário**
   Se houver caminhos específicos na conversa, trate-os como prioridade.

Declare no relatório qual contexto foi escolhido. Se houver ambiguidade, use o contexto mais conservador e explique a decisão.

## Descobrir a estratégia oficial de execução

Antes de concluir a revisão, procure evidências de como subir e validar o projeto em arquivos como:

- `docker-compose.yml`
- `docker-compose.yaml`
- `compose.yml`
- `compose.yaml`
- `Dockerfile`
- `Makefile`
- `README.md`
- documentação de setup
- `composer.json`, `package.json`, `pyproject.toml`, `go.mod` ou equivalentes

Determine:

- como subir a aplicação
- como rodar testes
- quais serviços precisam estar ativos
- se há banco, cache, workers, filas ou mocks necessários
- se existe fluxo oficial de build, lint, typecheck ou smoke test

## Validação obrigatória por docker

Tente na seguinte ordem, conforme o projeto suportar:

1. `docker compose`
2. `docker-compose`
3. `docker build` + `docker run`

Objetivos mínimos da validação:

- subir dependências essenciais da aplicação quando fizer sentido
- executar a suíte de testes mais relevante para a mudança dentro do container correto
- executar lint, typecheck, build ou smoke tests quando forem parte relevante da qualidade

Registre sempre:

- comando executado
- objetivo
- resultado: sucesso, falha ou não executado
- resumo do output
- se a validação cobre ou não a área modificada

## Quando docker falhar

Se não for possível usar docker, é obrigatório:

- informar o comando tentado
- informar o erro encontrado
- informar a causa provável
- informar o impacto da limitação na confiança da revisão
- tentar alternativas compatíveis com o repositório sem violar a regra de não editar

Nunca fingir que algo foi executado.

## Mapeamento obrigatório do sistema impactado

Antes de concluir a análise, mapear o sistema impactado com foco em:

- controllers afetados
- models, forms e regras de negócio afetadas
- services, repositories, helpers ou use cases impactados
- views, templates e componentes de interface impactados
- rotas, formulários e fluxos de submissão afetados
- queries, comandos SQL e acesso a banco impactados
- validações, autorização, sanitização e escaping afetados
- contratos entre camadas e integrações impactadas
- testes existentes ou ausentes nas áreas impactadas
- dependências indiretas e pontos de regressão

O objetivo não é apenas revisar o diff, mas entender onde a alteração encosta no sistema.

## Auditoria obrigatória de bugs e erros lógicos

Procure explicitamente por:

- bugs funcionais
- erros lógicos
- inconsistências de regra de negócio
- validações incompletas
- condições incorretas
- fluxos mortos
- retornos impossíveis ou contraditórios
- estados não tratados
- tratamento insuficiente de erros
- regressão indireta
- dependências quebradas entre camadas
- uso incorreto de dados vindos de request, session, banco ou API

Priorize defeitos reais e risco operacional, não apenas estilo ou legibilidade.

## Auditoria obrigatória de arquitetura

Revise explicitamente os seguintes pontos arquiteturais:

- separação de responsabilidades entre controller, model, service, repository, helper, view e infra
- regra de negócio implementada na camada errada
- controllers gordos ou com coordenação excessiva
- models com responsabilidades de apresentação ou infraestrutura
- views com lógica de negócio relevante
- helpers utilitários escondendo regra crítica
- acoplamento excessivo entre camadas
- dependências circulares ou difíceis de testar
- contratos frágeis ou implícitos entre módulos
- duplicação estrutural que aumenta risco de divergência
- ausência de boundary clara entre domínio, aplicação e infraestrutura
- trechos difíceis de validar, mockar ou evoluir sem alto risco

Quando identificar problema de arquitetura:

- não proponha refatoração ampla por padrão
- prefira correção localizada e de menor risco
- se a causa raiz exigir reestruturação maior, descreva isso como recomendação separada

## Checklist obrigatório

Cubra explicitamente:

- funcionalidade: o comportamento solicitado parece entregue?
- regressão: algo existente pode ter quebrado?
- segurança básica: validação, autorização, output escaping, upload, SQL, segredos
- compatibilidade: linguagem, framework, banco, front legado e dependências sensíveis
- acessibilidade: foco, teclado, semântica e feedback de erro quando observável no código
- responsividade: mobile, tablet e desktop a partir da estrutura da view
- código: legibilidade, duplicação, acoplamento, manutenção e tratamento de erro
- arquitetura: fronteiras, responsabilidades, contratos e testabilidade
- performance: consultas, loops, renderizações, payloads e processamento evitável
- testes: cobertura mínima, coerência, lacunas e confiabilidade
- ux/ui: escaneabilidade, hierarquia, clareza de CTA e mensagens de erro ou sucesso

## Heurísticas por stack

Adapte a revisão ao stack real do projeto. Quando o repositório indicar PHP 8.2, Yii2, MySQL 5.7, Bootstrap legado, Composer, PHPUnit, Codeception, jQuery ou stacks semelhantes, revise explicitamente:

### PHP 8.2 e Yii2

- propriedades dinâmicas
- assinaturas inconsistentes
- warnings e deprecations
- nullability incorreta
- validação de inputs em controllers, models e forms
- regras de autorização e checagens de acesso
- queries montadas manualmente e risco de SQL inseguro
- uso correto de ActiveRecord, transactions e scenarios
- mensagens de erro expostas ao usuário
- dependências legadas que possam quebrar em PHP 8.2

### MySQL 5.7

- SQL incompatível com recursos de MySQL 8+ usados por engano
- `group by` ambíguo
- funções não suportadas
- recursos JSON não compatíveis
- window functions inexistentes
- impacto de índices ausentes em consultas novas
- regressão em migrations, defaults, collations e tamanhos de coluna

### Views, Bootstrap e responsividade

- mistura de classes Bootstrap 3 e 4 na mesma tela
- grids quebrados
- overflow horizontal
- tabelas sem responsividade
- foco visível
- navegação por teclado
- labels e semântica básica
- clareza de CTA
- hierarquia visual
- estados de erro e sucesso
- dependências jQuery ou plug-ins que possam quebrar com markup novo

### Testes

- verificar se a mudança alterou regra de negócio sem cobertura mínima
- revisar testes alterados e lacunas óbvias
- preferir testes ligados diretamente à mudança
- se não houver testes relevantes, declarar a lacuna como risco

## Comandos e abordagem recomendados

Use o `container` para inspecionar o repositório. Prefira comandos de leitura como:

- `git status --short`
- `git branch --show-current`
- `git rev-parse --abbrev-ref --symbolic-full-name @{u}` quando existir
- `git diff --stat`
- `git diff --name-only`
- `git diff -- <arquivo>`
- `git show --stat --name-only HEAD`
- `git show HEAD -- <arquivo>`
- `sed -n 'start,endp' <arquivo>`
- `grep -n "padrao" <arquivo>`
- `find` apenas quando necessário para mapear áreas impactadas
- comandos de docker apenas para validação reproduzível
- busca por referências cruzadas entre controllers, models, views, services, testes e configurações

Leia apenas o necessário para sustentar os achados. Cite arquivo e linha sempre que possível.

## Como priorizar

Priorize achados de:

1. alto impacto e baixo risco de correção
2. regressão funcional, segurança e integridade
3. erros lógicos e regras de negócio
4. erros de arquitetura com correção localizada viável
5. incompatibilidades de stack
6. problemas de testes, UX, performance e manutenção

Evite sugerir refatorações amplas quando uma correção localizada resolver.

## Saída obrigatória da Etapa 1

Entregue em markdown e use exatamente esta estrutura:

## Parecer

`Aprovado`, `Aprovado com ressalvas` ou `Reprovado`

## Resumo executivo

Visão rápida do estado geral, contexto analisado, nível de risco, qualidade geral da implementação, status da validação via docker e nível de confiança da revisão: `alto`, `médio` ou `baixo`.

## Contexto analisado

- caminho do projeto
- descrição da tarefa
- contexto de git escolhido
- estratégia oficial de execução encontrada
- módulos e fluxos mapeados

## Evidências de execução

Para cada validação executada:

- objetivo
- comando executado
- resultado: sucesso, falha ou não executado
- evidência resumida
- impacto no diagnóstico

## Mapeamento do sistema impactado

Liste os componentes afetados ou potencialmente afetados, por camada.

## Achados por severidade

Para cada achado informe:

- onde está: arquivo + linha ou faixa de linhas
- arquivo em formato clicável quando o ambiente suportar
- por que é um problema
- se é bug funcional, erro lógico, arquitetura, regressão, segurança, compatibilidade, performance, acessibilidade, teste ou UX
- severidade: alta, média ou baixa
- impacto esperado

## Arquivos com erro ou risco

Para cada arquivo:

- caminho
- arquivo em formato clicável quando o ambiente suportar
- finalidade aparente
- problema encontrado ou risco identificado
- área impactada

## Cobertura da revisão

- o que foi validado por teste
- o que foi validado por leitura de código
- o que não foi possível validar

## Correções recomendadas

Liste em ordem priorizada, com foco em baixo risco primeiro.

## Sugestões de patch

Não aplique nada. Forneça apenas patches sugeridos, pseudodiff ou instruções objetivas por arquivo.

## Plano estratégico de correção

Explique:

- o que será corrigido
- em que arquivo será corrigido
- por que a correção será feita nesse ponto
- ordem recomendada de execução
- validações mínimas após cada correção

## Plano de execução após aprovação

Liste passos curtos, incrementais e verificáveis. Cada passo deve incluir como validar.

## Status

Aguardando aprovação

## Saída obrigatória da Etapa 2

Somente após aprovação explícita do usuário, entregar em markdown com esta estrutura:

## Status da execução

`Correções aplicadas`

## Resumo das correções

Visão geral do que foi corrigido, do que foi validado novamente e do risco residual.

## Arquivos alterados

Para cada arquivo:

- caminho
- arquivo em formato clicável quando o ambiente suportar
- o que foi alterado
- por que foi alterado
- tipo de correção: bug, lógica, arquitetura, regressão, compatibilidade, segurança, performance, UX ou teste
- impacto esperado da alteração

## Relatório detalhado das mudanças

Descreva por arquivo, de forma objetiva:

- antes
- depois
- motivo técnico da mudança
- efeito esperado

## Validação técnica realizada

Informar quais verificações técnicas foram feitas após a alteração, incluindo comandos executados quando houver.

## Riscos remanescentes

Liste o que ainda merece atenção.

## Próximos passos recomendados

Liste passos curtos e verificáveis.

## Regras de escrita

- Seja objetivo, técnico e verificável.
- Não invente contexto ausente; declare incerteza.
- Quando não houver número de linha exato, informe o bloco ou função e diga que a linha é aproximada.
- Quando houver pouco contexto, entregue ao menos diagnóstico claro, lista de arquivos afetados, explicação dos erros e plano de correção.
- Não esconda riscos relevantes por falta de teste. Declare a lacuna.
- Nunca misture a etapa de auditoria com a etapa de correção sem aprovação explícita.
- Quando a validação via docker não for possível, deixe isso explícito no resumo executivo, nas evidências de execução e no nível de confiança.
