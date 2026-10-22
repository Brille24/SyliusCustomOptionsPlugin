# Local development with Docker

The stack runs PHPUnit, Behat (including `@javascript`), ECS, and PHPStan in throwaway containers, and serves the official Sylius Test Application in the browser. PHP 8.3, Node 22, MySQL 8.0, nginx, and Xdebug are included. Xdebug is installed but **off by default**.

Host PHP, Composer, Node, and MySQL are not required.

## Services

| Service | Role | Host port |
| --- | --- | --- |
| `mysql` | MySQL 8.0 (`sylius_dev`, `sylius_test`) | internal |
| `php` | CLI image for tests and `bin/console` (`docker compose run --rm`) | — |
| `php-fpm` | Serves the Test Application through nginx | internal |
| `nginx` | Shop/admin in the browser | `8080` (dev), `8081` (test / Behat JS). Override with `WEB_DEV_PORT` / `WEB_TEST_PORT`. |
| `chrome` | Headless Chromium for Behat `@javascript` (`--profile js`) | internal |

The plugin is bind-mounted at `/plugin`. CLI containers run as your host UID/GID. php-fpm workers use the same IDs baked in at **image build**. If your UID is not 1000, build once with:

```bash
HOST_UID="$(id -u)" HOST_GID="$(id -g)" docker compose build
```

Then use `docker/bin/start` as usual. Rebuild after changing user IDs so `var/` stays writable for both CLI and the browser.

## Quick start

```bash
docker/bin/start
docker/bin/setup
```

Then open:

- Shop: http://127.0.0.1:8080
- Admin: http://127.0.0.1:8080/admin — `sylius@example.com` / `sylius`

If those host ports are already in use, start with different mappings. Internal Behat URLs stay on `nginx:8081`.

```bash
WEB_DEV_PORT=8082 WEB_TEST_PORT=8083 docker/bin/start
```

`setup` installs Composer and Yarn dependencies, creates `sylius_dev` and `sylius_test`, runs migrations plus `doctrine:schema:update` for plugin tables, builds frontend assets, and loads fixtures into `dev`.

On this Sylius 2 branch the plugin listeners and templates are not fully migrated yet. `sylius:fixtures:load` and many PHPUnit/Behat cases can fail (for example `PrePersistEventArgs::getEntityManager()`). The stack still starts the shop at `/en_US/` and `/admin` through nginx; those pages may 500 until the plugin code is updated.

Stop the stack with `docker/bin/stop`. Pass `-v` to drop the MySQL volume as well.

## Running tests

```bash
docker/bin/phpunit
docker/bin/behat --tags='~@javascript'
docker/bin/behat --tags=@javascript
docker/bin/analyse
```

`docker/bin/test` runs those suites in order. The Behat wrapper starts the `js` profile (Chrome) when `@javascript` is in the arguments.

Other wrappers:

```bash
docker/bin/console cache:clear --env=dev
docker/bin/composer install
docker/bin/php -v
docker/bin/sh
```

Coverage (Xdebug coverage mode, not the debugger):

```bash
XDEBUG_MODE=coverage docker/bin/phpunit --coverage-html var/coverage
```

## Xdebug

Xdebug 3 is compiled in. `xdebug.mode` defaults to `off`. The IDE key is `PHPSTORM`. The container connects to `host.docker.internal:9003`.

### Debug CLI tests and commands

Leave php-fpm alone. Prefix the wrapper with the mode and session:

```bash
XDEBUG_MODE=debug XDEBUG_SESSION=PHPSTORM docker/bin/phpunit --filter SomeTest
XDEBUG_MODE=debug XDEBUG_SESSION=PHPSTORM docker/bin/behat features/some.feature
XDEBUG_MODE=debug XDEBUG_SESSION=PHPSTORM docker/bin/console debug:container --env=dev
```

Start listening in PhpStorm first (see below).

### Debug browser requests

```bash
docker/bin/xdebug on
```

That sets `xdebug.mode=debug` and `start_with_request=trigger` on php-fpm. Then either:

- enable the [Xdebug Helper](https://www.jetbrains.com/help/phpstorm/browser-debugging-extensions.html) browser extension with IDE key `PHPSTORM`, or
- add `?XDEBUG_SESSION=PHPSTORM` to the URL.

Open http://127.0.0.1:8080 as usual.

### Debug Behat `@javascript` requests

Headless Chrome does not send the trigger cookie. Start php-fpm in “every request” mode:

```bash
docker/bin/xdebug on --every-request
XDEBUG_MODE=debug XDEBUG_SESSION=PHPSTORM docker/bin/behat --tags=@javascript
```

### Turn Xdebug off

```bash
docker/bin/xdebug off
docker/bin/xdebug status
```

`status` prints the active mode for php-fpm and a one-shot CLI container.

### Profiling

```bash
XDEBUG_MODE=profile docker/bin/phpunit --filter SomeTest
```

Cachegrind files land in `var/xdebug/`.

## PhpStorm setup

### Listen for debug connections

1. **Settings → PHP → Debug**
   - Xdebug debug port: `9003`
   - Check **Can accept external connections**
2. Start listening: **Run → Start Listening for PHP Debug Connections** (phone icon in the toolbar).

### Path mappings (browser and Behat JS)

1. **Settings → PHP → Servers**
2. Add a server:
   - Name: `sylius-custom-options` (must match `PHP_IDE_CONFIG=serverName=sylius-custom-options`)
   - Host: `localhost`
   - Port: `8080`
   - Debugger: Xdebug
   - Check **Use path mappings**
   - Project root → `/plugin`

Use the same mapping if you add a second server for port `8081`.

### CLI interpreter

1. **Settings → PHP → CLI Interpreter → … → From Docker, Vagrant, VM, WSL, Remote → Docker Compose**
2. Configuration file: `compose.yaml`
3. Service: `php`
4. Lifecycle: **Run** (or **Exec** after `docker/bin/start` if you also keep a `php` container up)

PhpStorm should pick PHP 8.3 with the Xdebug extension listed.

### PHPUnit and Behat from the IDE

1. **Settings → PHP → Test Frameworks**
2. Add **PHPUnit by Remote Interpreter** using the Compose interpreter.
   - Path to script: `vendor/bin/phpunit`
   - Configuration: `phpunit.xml.dist`
3. Add **Behat by Remote Interpreter**.
   - Behat executable: `vendor/bin/behat`
   - Configuration: `behat.yml.dist`

You can then debug a test from the gutter. The IDE sets `XDEBUG_MODE=debug` and starts listening.

### Linux firewall

Xdebug connects **from the container to the host** on port 9003. Allow that inbound traffic from the Docker bridge, for example:

```bash
sudo iptables -I INPUT -i docker0 -p tcp --dport 9003 -j ACCEPT
```

or an equivalent `firewalld` / `nft` rule. `extra_hosts` maps `host.docker.internal` to the host gateway, so no extra compose setting is needed on Linux.

### Troubleshooting

- Confirm the extension is loaded and off by default: `docker/bin/php -v` should mention Xdebug, and `docker/bin/xdebug status` should print `off` until you enable it.
- Raise the log level: `XDEBUG_CONFIG='log_level=7' XDEBUG_MODE=debug XDEBUG_SESSION=PHPSTORM docker/bin/php -r 'echo 1;'`
- Read `/plugin/var/xdebug/xdebug.log` (also `var/xdebug/xdebug.log` on the host).
- If PhpStorm never stops: check the server name, path mapping to `/plugin`, and that port 9003 is reachable from Docker.
- After `docker/bin/xdebug on`, `docker/bin/xdebug status` must show `debug` for php-fpm. Browser requests still need the trigger cookie unless you used `--every-request`.
