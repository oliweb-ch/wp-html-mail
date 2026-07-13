# WP HTML Mail — Email Template Designer

> **Fork** of [codemiq/wp-html-mail](https://github.com/codemiq/wp-html-mail) maintained by [oliweb-ch](https://github.com/oliweb-ch).
> Webfonts addon merged into core. Drop-in replacement — same WordPress folder slug `wp-html-mail`.

Design professional HTML email templates for every outgoing WordPress email, with full control over layout, typography, colors, header, and footer — without touching a single line of code.

---

## Requirements

| Requirement | Minimum |
|---|---|
| WordPress | 6.4 |
| PHP | 8.3 |

---

## What's new in v4.0.0

- **PHP 8.3+ required** — dropped support for PHP < 8.3
- **WordPress 6.4+ required** — dropped support for older WP
- **Webfonts merged into core** — the companion addon is no longer a separate plugin; Google Fonts configuration is now built-in
- **`voku/css-to-inline-styles` replaced** by [`tijsverkoyen/css-to-inline-styles`](https://github.com/tijsverkoyen/CssToInlineStyles) (actively maintained, last updated December 2025)
- **Automatic updates via GitHub Releases** — no WordPress.org required (see [Updates](#updates))
- **Bug fix**: `class-mailbuilder.php` was never loaded in the original plugin
- **Bug fix**: wrong textdomain `haet_mail` corrected to `wp-html-mail`
- **Bug fix**: PHP 8.2 deprecation `&$this` in hook callbacks removed

---

## Installation

This plugin is **not distributed on WordPress.org**. Install manually or via Composer.

### Manual install

1. Download the latest `.zip` from [GitHub Releases](https://github.com/oliweb-ch/wp-html-mail/releases)
2. In WordPress admin: **Plugins → Add New → Upload Plugin**
3. Upload the `.zip` and activate

> The plugin folder must be named `wp-html-mail` for the drop-in replacement to work.

### Via WP-CLI

```bash
wp plugin install https://github.com/oliweb-ch/wp-html-mail/releases/latest/download/wp-html-mail.zip --activate
```

---

## Updates

Automatic updates are handled by [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker) pointing to GitHub Releases. Once the plugin is active, update notifications appear in the WordPress dashboard like any other plugin.

---

## Features

- Visual template designer (header, body, footer, colors, fonts)
- CSS-to-inline-styles for maximum email client compatibility
- Per-plugin customization: override template per sender plugin
- Google Fonts support (built-in, up to 3 font sets)
- Responsive design with mobile-specific CSS overrides
- WPMandrill compatibility

### Supported integrations

The template designer detects emails from these plugins and allows per-plugin template overrides:

- Contact Form 7
- Ninja Forms
- Gravity Forms
- Formidable Forms
- HappyForms
- Caldera Forms
- Fluent CRM
- Fluent Support
- The Newsletter Plugin
- WP eCommerce
- Tera Wallet (WooWallet)
- WP Foro
- Divi
- WP Support Plus
- Ultimate WP Mail
- Birthday Emails

---

## Developer hooks

### Filters

| Filter | Description |
|---|---|
| `haet_mail_fonts` | Add or remove fonts from the font selector |
| `haet_mail_header` | Modify the rendered header HTML |
| `haet_mail_footer` | Modify the rendered footer HTML |
| `haet_mail_preheader` | Modify the preheader text |
| `haet_mail_modify_styled_mail` | Filter the full email HTML before sending |
| `haet_mail_css_desktop` | Add custom CSS for desktop |
| `haet_mail_css_mobile` | Add custom CSS for mobile (media query) |
| `haet_mail_link_header` | Whether to make the header image a link (`true`/`false`) |
| `haet_mail_demo_content` | Override the demo content in the preview |
| `haet_mail_react_components` | Register additional React micro-frontends in the settings UI |

### Actions

| Action | Description |
|---|---|
| `haet_mail_rest_api_init` | Fires when REST routes are registered; receives the API namespace as argument |
| `haet_mail_before_settings_tab_template` | Fires before the template tab renders (used to inject stylesheets) |
| `haet_mail_plugin_reset_actions` | Fires when per-plugin settings are reset |
| `haet_mail_process_advanced_actions` | Fires during advanced settings processing |

---

## Google Fonts (Webfonts)

Configure up to 3 Google Font sets in **Settings → HTML Mail → Webfonts**. Each font set defines:

- A Google Font family
- A fallback system font
- A display name used in the font selector

The plugin injects the Google Fonts stylesheet into both the admin template designer and the outgoing email HTML.

> To refresh the bundled Google Fonts list during development, define `HAET_MAIL_WEBFONTS_API_KEY` in `wp-config.php` with a valid Google Fonts API key and call `Haet_Webfonts()->refresh_google_fonts_list()`.

---

## Development

```bash
git clone https://github.com/oliweb-ch/wp-html-mail.git
cd wp-html-mail
composer install
```

Dependencies managed via Composer. The `vendor/` directory is committed to the repository so the plugin works without Composer on production servers.

---

## Credits

Original plugin by **Hannes Etzelstorfer // [codemiq](https://codemiq.com)** — Copyright 2025 codemiq.
Fork maintained by **[oliweb-ch](https://github.com/oliweb-ch)** — Copyright 2025 oliweb-ch.

---

## License

[GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)
