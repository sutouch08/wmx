# Copilot instructions for this repository

## Commands

- Install PHP dependencies with `composer install`.
- There is no repo-specific build step; this is a server-rendered CodeIgniter 3 application.
- There is no committed `phpunit.xml` or `tests/` tree in the repository. The only declared test runner dependency is PHPUnit from `composer.json`; if you add or run a direct test file, use `vendor\bin\phpunit path\to\Test.php`.
- For targeted validation on edited PHP files, use `php -l path\to\File.php`.

## High-level architecture

- This repository is a CodeIgniter 3 application rooted at `index.php`, with `ENVIRONMENT` taken from `CI_ENV`. `application/` contains the app code and `system/` is the framework.
- The default route is `main`; most other pages rely on CodeIgniter's conventional `controller/method` routing rather than explicit route declarations.
- The app is organized by business domain under `application/controllers`, `application/models`, and `application/views`. The main domains are `masters`, `orders`, `inventory`, `purchase`, `report`, `account`, `users`, `mobile`, and `rest`.
- Browser controllers usually extend `PS_Controller`, which centralizes login checks, loads the current user from the `uid` cookie, applies maintenance/password-expiry redirects, and computes `$this->pm` permissions from each controller's `menu_code`.
- Shared page chrome lives in `application/views/include/header.php` and `footer.php`. Those templates expect controller properties like `$this->title`, `$this->home`, and `$this->pm`, and the header renders `deny_page` automatically when `can_view` is false.
- Mobile flows are not just responsive views. There is a separate `application/controllers/mobile` namespace plus `application/views/mobile/...`, and some desktop controllers also branch on `user_agent` to redirect into mobile-specific flows.
- API integrations live under `application/controllers/rest/api` and `application/controllers/rest/V1`, using `application/libraries/REST_Controller.php`. API controllers commonly gate themselves with config flags from the `config` table and write request/response logs through the secondary `logs` database connection.
- The app also uses additional named database connections beyond the default one (`logs`, `wms`) for API logging and warehouse-related operations.

## Key conventions

- Keep application code compatible with this older CodeIgniter/PHP style. `composer.json` targets PHP `>=5.3.7`, and the existing codebase uses classic CI patterns rather than modern PHP features such as scalar type hints, return types, namespaces in app code, or framework-independent DI.
- Follow the repository formatting already in use: tabs for indentation, LF line endings, UTF-8 files, and Allman-style braces.
- UI controllers almost always declare `menu_code`, `menu_group_code`, `title`, and `home` properties. New back-office pages should fit that pattern so permission checks and shared layout continue to work.
- List pages follow a shared filter/pagination pattern: gather filters with `get_filter()`, persist them in cookies, read page size with `get_rows()`, build pagination with `pagination_config()`, and expose a `clear_filter()` action that deletes the related cookies.
- The helpers in `application/helpers` are part of the app's core API surface, not incidental utilities. `users`, `menu`, and `tools` are autoloaded, so controllers and views call global helpers like `get_permission()`, `getConfig()`, `get_null()`, `number()`, and `pagination_config()` directly.
- Many views are intentionally thin and rely on controllers/helpers to precompute display-ready values, including HTML fragments and formatted strings. Match that pattern instead of moving formatting logic into templates.
- Models are typically thin `CI_Model` wrappers around one table, often with a `$tb` property plus methods like `count_rows()`, `get_list()`, `add()`, and `update()`. Controllers commonly compose several models and helpers to build the final page or API response.
- Repository-specific configuration is stored in the database `config` table and read through `getConfig()`. Before hard-coding behavior flags, check whether an existing config key should drive the behavior instead.
- Business-facing text in controllers and views is often Thai. Preserve the existing language used by the surrounding file when changing labels, errors, or user-visible messages.
