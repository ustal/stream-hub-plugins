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

### `MessageComposerPlugin`

This plugin is opt-in. It attaches to `dialog.bottom` by default and renders a message form widget intended for project-level controller integration.

It includes:

- `SendMessageCommand`
- `SendMessageCommandHandler`
- `MessageEventFactory`
- `MessageComposerWidget`

The widget expects the consuming application to provide at least these stream-context values:

- `stream_hub.message_composer.stream_id`
- `stream_hub.message_composer.action_url`

Optional context keys can override placeholder, submit label, field names, and CSRF intention.

It also declares one named identifier generator requirement:

- `event_id`

The consuming integration is expected to map that requirement explicitly. In the Symfony bundle this is done under `stream_hub.id_generators.message-composer.event_id`.

## Template Overrides

Widgets in this package declare their default templates statically, but project-level overrides are supported through `WidgetTemplateResolverInterface` from `stream-hub-core`.

That means a consuming application can override the template used for a widget without modifying the widget class itself.

## Starter Stack

The official Symfony bundle currently treats these three plugins as the default starter stack:

- `TwoColumnLayoutPlugin`
- `SidebarScaffoldPlugin`
- `DialogScaffoldPlugin`

`MessageComposerPlugin` is intentionally not enabled by default because it requires application-level form handling and request/controller integration.

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
