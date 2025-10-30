## tool_sentry — Integração do Moodle com Sentry (PHP e Browser)

Plugin de administração para enviar erros e logs do Moodle ao Sentry, com suporte a injeção do SDK no navegador, breadcrumbs de eventos e Session Replay.

### Recursos
- Integração com o SDK PHP do Sentry (via Composer)
- Injeção opcional do SDK de navegador e configuração pelo Loader do Sentry
- Captura automática do último erro PHP do request
- Encaminhamento automático de erros/avisos de PHP para o Sentry (opt‑in)
- Breadcrumbs de eventos do Moodle (opt‑in)
- API simples de logs: `\tool_sentry\helper::log($level, $message, $context = [])`
- Session Replay (amostragem configurável)
- Botão de teste para envio de exceção pela interface de administração

### Requisitos
- Moodle 3.11 ou superior (versão mínima: `2021051700`)
- PHP 7.4+
- Conta/projeto no Sentry com DSN (servidor) e, opcionalmente, JavaScript Loader

### Instalação
1. Copie o diretório para `admin/tool/sentry` no seu Moodle.
2. Execute a atualização do Moodle acessando a interface administrativa ou via CLI.
3. Certifique‑se de que o `vendor/` está presente (Composer já incluso neste pacote).

### Configuração
No Moodle: Administração do site → Plugins → Ferramentas de administração → Sentry Configuration.

Campos principais:
- DSN do Servidor (obrigatório): `tool_sentry/dsn`
- Ativar plugin: `tool_sentry/activate`
- Loader JavaScript (opcional, para navegador): `tool_sentry/javascriptloader`
- Opções do SDK (amostragem, ambiente, breadcrumbs, etc.)
- Encaminhar logs de PHP e adicionar breadcrumbs: `tool_sentry/auto_hook`
- Habilitar logs personalizados via API: `tool_sentry/log_messages`

Session Replay (navegador):
- Defina o Loader JavaScript do Sentry (por exemplo, `https://js.sentry-cdn.com/<public>.min.js`)
- Configure as taxas:
  - `tool_sentry/replays_session_sample_rate` (sugestão: 0)
  - `tool_sentry/replays_on_error_sample_rate` (sugestão: 1.0)

Observação: As opções de Replay e integradores de navegador não são enviadas ao SDK PHP; elas são aplicadas apenas no cliente.

### Teste rápido
Na mesma página de configurações existe um botão “Send Exception”. Clique para gerar e enviar uma exceção de teste ao Sentry.

### Uso da API de logs
No código PHP do Moodle (após configurar o plugin):

```php
\tool_sentry\helper::log('info', 'Algo aconteceu', ['userId' => $USER->id ?? null]);
// níveis aceitos: 'debug', 'info', 'warning', 'error', 'fatal'
```

Ative “Enable custom logs” nas configurações para habilitar esse envio.

### Configuração via CLI
Exemplos:

```bash
php admin/cli/cfg.php --component=tool_sentry --name=activate --set=1
php admin/cli/cfg.php --component=tool_sentry --name=dsn --set="https://<key>@<host>/<project>"
php admin/cli/cfg.php --component=tool_sentry --name=javascriptloader --set="https://js.sentry-cdn.com/<public>.min.js"
php admin/cli/cfg.php --component=tool_sentry --name=auto_hook --set=1
php admin/cli/cfg.php --component=tool_sentry --name=replays_on_error_sample_rate --set=1
```

### Privacidade
O plugin não armazena dados pessoais localmente. Ao enviar eventos ao Sentry, informações como IP e contexto podem ser incluídas dependendo das opções do SDK (por exemplo, “Send Default PII”). Revise e ajuste essas opções conforme a sua política de privacidade.

### Compatibilidade
- Testado no Moodle 4.x
- SDK do Sentry: `sentry/sdk` ^3.3

### Suporte e Licença
- Autor: Giovanne Oliveira <giovanne@giovanne.dev>
- Licença: GNU GPL v3 ou posterior (`http://www.gnu.org/copyleft/gpl.html`)