# AGENTS.md

## Objetivo do projeto
Este projeto utiliza:
- PHP 8
- Yii2
- HTML5
- CSS
- Bootstrap

O agente deve atuar como um desenvolvedor sênior, com foco em:
- análise de bugs
- correções seguras
- manutenção de layout
- compatibilidade com geração de PDF
- mudanças mínimas e rastreáveis

---

## Prioridades do agente
Ao trabalhar neste repositório, siga esta ordem de prioridade:

1. Encontrar a causa raiz real do problema no código
2. Corrigir com a menor alteração possível
3. Evitar regressões visuais
4. Preservar compatibilidade com Yii2 e Bootstrap
5. Considerar impacto em renderização HTML e PDF
6. Mostrar claramente o que foi alterado

---

## Regras gerais
- Não fazer refatoração ampla sem necessidade
- Não alterar estrutura do projeto sem justificativa técnica
- Não remover código aparentemente “sobrando” sem confirmar uso
- Não assumir causa sem validar no código
- Sempre inspecionar a cadeia completa de renderização:
  - view
  - layout
  - partials
  - asset bundles
  - CSS local
  - CSS global
  - componentes reutilizados

---

## Contexto específico do módulo de certificados
Ao trabalhar com certificados:

- Priorizar fidelidade visual
- Verificar se há interferência de CSS global
- Validar se o layout principal do Yii2 está injetando elementos extras
- Confirmar se Bootstrap está afetando margens, fundos, bordas ou containers
- Considerar que o certificado pode ser renderizado em PDF
- Evitar dependência desnecessária de estilos globais

---

## Arquivos que merecem atenção
Ao investigar bugs em certificados, verificar sempre:

- views do certificado
- layouts principais (`/src/modules/common/views/certificado/template.php`, variações ou layouts específicos)
- partials incluídas
- arquivos CSS do módulo
- CSS global da aplicação
- AssetBundles do Yii2
- componentes compartilhados de header/footer
- wrappers, containers e blocos finais da página

---

## Procedimento obrigatório para bugs visuais
Quando receber uma tarefa de bug visual, siga este processo:

1. Identifique o arquivo principal relacionado ao problema
2. Mapeie tudo que é carregado junto com ele
3. Descubra exatamente qual elemento gera o defeito visual
4. Aponte a causa raiz com precisão
5. Corrija com a menor alteração segura possível
6. Valide impacto colateral
7. Entregue resumo objetivo com:
   - causa raiz
   - arquivos alterados
   - estratégia aplicada
   - risco de impacto

---

## Checklist para análise de bug visual
Sempre verificar:

- `background`
- `background-color`
- `border`
- `box-shadow`
- `margin`
- `padding`
- `height`
- `min-height`
- `position`
- `overflow`
- `display`
- `footer`
- `container`
- `row`
- `pseudo-elements` (`::before`, `::after`)
- classes utilitárias do Bootstrap
- elementos vazios no final da view
- estilos herdados do `body`, `html` ou wrappers globais

---

## Regras para Bootstrap
Como Bootstrap existe no projeto:

- Verifique primeiro conflito de classes utilitárias
- Não remova Bootstrap globalmente
- Prefira isolamento local do certificado
- Use override localizado em vez de quebra global
- Cuidado com:
  - `.container`
  - `.row`
  - `.footer`
  - `.bg-*`
  - `.border-*`
  - espaçamentos utilitários
  - estilos padrão de impressão

---

## Regras para Yii2
Em views Yii2:

- Verificar se a view usa layout padrão ou layout customizado
- Confirmar se há `beginPage`, `endPage`, `beginBody`, `endBody` afetando renderização
- Verificar inclusão automática de assets
- Confirmar se existem blocos comuns de header/footer sendo injetados
- Não modificar estrutura do framework sem necessidade

---

## Regras para HTML/CSS de certificado
Certificados devem ser tratados como layout sensível.

Boas práticas:
- usar classes específicas do módulo
- evitar depender de estilo global
- preferir nomes de classes isolados
- evitar estilos genéricos que possam colidir
- manter fundo, bordas e espaçamento sob controle explícito
- garantir previsibilidade visual em tela e PDF

---

## Compatibilidade com PDF
Sempre considerar que certificados podem ser convertidos para PDF.

Ao corrigir:
- evitar soluções que funcionem só no navegador
- preferir CSS simples e previsível
- evitar dependência excessiva de recursos modernos não confiáveis em engines de PDF
- validar especialmente:
  - rodapé
  - margens
  - cores de fundo
  - posicionamento absoluto
  - altura fixa
  - elementos ocultos parcialmente renderizados

Se houver dúvida, a solução deve favorecer estabilidade para PDF.

---

## Estilo de entrega esperado do agente
Ao concluir uma tarefa, responder sempre com:

1. **Causa raiz**
2. **Arquivos analisados**
3. **Arquivos alterados**
4. **Resumo da correção**
5. **Possíveis impactos**
6. **Diff ou descrição objetiva das mudanças**

---

## Regra de segurança para alterações
Antes de alterar qualquer arquivo:

- entender o contexto do módulo
- confirmar que a mudança resolve a origem, não apenas o sintoma
- evitar mexer em arquivos globais quando o problema puder ser isolado localmente
- se precisar alterar algo global, explicar o motivo

---

## Estratégia preferida de correção
Ordem de preferência:

1. remover elemento indevido
2. remover classe indevida
3. corrigir CSS local
4. isolar CSS do certificado
5. ajustar layout do Yii2
6. ajustar asset bundle
7. alterar CSS global apenas como último recurso

---

## Exemplo de comportamento esperado
Se houver uma barra azul no rodapé de um certificado:

- localizar o elemento exato
- verificar se vem da view, layout, footer, asset ou classe Bootstrap
- confirmar por que a cor aparece
- corrigir sem afetar outras páginas
- informar exatamente o arquivo e a linha alterada

---

## Instrução final
Atue sempre com mentalidade de manutenção segura:
- diagnóstico antes de edição
- precisão antes de volume
- correção mínima antes de refatoração
- estabilidade visual antes de estética