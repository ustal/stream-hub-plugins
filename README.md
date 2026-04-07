# stream-hub-plugins

[![CI](https://github.com/ustal/stream-hub-plugins/actions/workflows/ci.yml/badge.svg)](https://github.com/ustal/stream-hub-plugins/actions/workflows/ci.yml)

Starter plugin pack for the Stream Hub ecosystem.

Each plugin is intended to be self-contained: one plugin root folder should hold its classes, resources, templates, and assets so it can be moved into a standalone repository later with minimal reshuffling.

## Included Plugins

### `TwoColumnLayoutPlugin`

The first plugin in this repository is a simple layout container:

- it attaches to `DefaultSlotName::MAIN` by default;
- it provides two child slots:
  - `layout.left`
  - `layout.right`
- it ships a baseline CSS file for desktop and mobile layouts;
- it also includes a Twig template for the future Symfony/Twig bridge.

Its structure is plugin-centric:

```text
src/TwoColumnLayout/
  TwoColumnLayoutPlugin.php
  Enum/
  Widget/
  Resources/
    assets/
    views/
```

This plugin is intentionally minimal. Its purpose is to establish a reusable layout shell so that later plugins can render into the left and right columns.

### `SidebarScaffoldPlugin`

This plugin attaches to the left side of `TwoColumnLayoutPlugin` and provides three semantic sidebar slots:

- `sidebar.filter`
- `sidebar.search`
- `sidebar.list`

It is intentionally only a scaffold. Real project-specific filter/search/list widgets are expected to be injected into these slots by the consuming application or by additional plugins.

### `DialogScaffoldPlugin`

This plugin attaches to the right side of `TwoColumnLayoutPlugin` and provides three semantic dialog slots:

- `dialog.top`
- `dialog.middle`
- `dialog.bottom`

It is also a scaffold, intended to host project-specific dialog title, message stream, and input widgets.

## Template Overrides

Widgets in this package declare their default templates statically, but project-level overrides are supported through `WidgetTemplateResolverInterface` from `stream-hub-core`.

That means a consuming application can override the template used for a widget without modifying the widget class itself.

## Starter Stack

The official Symfony bundle currently treats these three plugins as the default starter stack:

- `TwoColumnLayoutPlugin`
- `SidebarScaffoldPlugin`
- `DialogScaffoldPlugin`

## Development

Install dependencies against the local workspace:

```bash
make install
```

Run tests:

```bash
make test
```

Run deptrac:

```bash
make deptrac
```
