# stream-hub-plugins

[![CI](https://github.com/ustal/stream-hub-plugins/actions/workflows/ci.yml/badge.svg)](https://github.com/ustal/stream-hub-plugins/actions/workflows/ci.yml)

Framework-agnostic feature modules for Stream Hub.

In the `v1` direction this repository is no longer a UI plugin pack. It now holds headless modules made of commands, handlers, and small workflow services.

## Included Module

### `MessageComposer`

Current contents:

- `SendMessageCommand`
- `SendMessageCommandHandler`
- `MessageEventFactory`

The handler:

- builds a `StreamEvent` for a user message;
- dispatches low-level `AppendStreamEventCommand` through `ModelCommandBusInterface`;
- does not talk to the backend directly.

The module expects an event identifier generator to be injected by the consuming integration.

## Development

Install dependencies:

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
