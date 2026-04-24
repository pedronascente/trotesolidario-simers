# 🚀 Nova tarefa --- Cadastro de Participante (Módulo Participante)

## 📍 Contexto

URL: http://localhost:8080/participante/register/index?cpf={cpf}

## 🎯 Objetivo

Implementar ou evoluir o fluxo de cadastro de participante, garantindo
validações, reaproveitamento de código existente, autenticação
automática e testes via Docker.

## 📋 Campos do formulário

-   Nome completo\
-   CPF\
-   E-mail\
-   Senha\
-   Você é estudante? (sim \| não)\
-   Você é estudante de medicina? (sim \| não)\
-   Previsão de formatura

Campos automáticos: - user_name (nome.sobrenome) - perfil = participante

Checkboxes: - Política de privacidade - Uso de imagem, vídeo e voz

## ⚙️ Regras de negócio

-   CPF e e-mail únicos
-   Validações completas
-   Criação de usuário e participante
-   Autenticação automática
-   Redirecionamento para /participante/default/home

## 🔎 ETAPA 1 --- ANÁLISE

-   Verificar se já existe implementação
-   Analisar e refatorar sobre o existente
-   Mapear arquitetura, controllers, services
-   Definir plano funcional e técnico
-   Listar arquivos criados/alterados (com links)
-   Identificar riscos

Finalizar com: AGUARDANDO APROVAÇÃO PARA IMPLEMENTAR

## 🛠️ ETAPA 2 --- IMPLEMENTAÇÃO

-   Reutilizar código existente
-   Não duplicar lógica
-   Manter padrões do projeto

## 🧪 Testes (Docker)

Testar: - cadastro com sucesso - CPF duplicado - e-mail duplicado -
erros de validação - autenticação automática

## 📦 Entregáveis

-   arquivos criados e alterados (com links)
-   explicação das mudanças
-   fluxo final
-   evidência de testes Docker
