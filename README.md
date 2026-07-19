# WP Abilities Permission Auditor

> **Document status:** implementation-complete engineering blueprint, not a claim that the software has already been built.

A defensive inventory and review tool for WordPress Abilities API registrations and their automation/MCP exposure, with version guards and least-privilege evidence.

## Who this is for

- plugin developers exposing abilities
- security reviewers
- site administrators
- AI/automation integration teams

## Required implementation outputs

- WordPress plugin
- WP-CLI commands
- audit core package
- reporters
- policy schema
- safe/vulnerable fixtures
- threat model
- MCP exposure guide

## Non-negotiable rule

A feature is not complete because code exists. It is complete only when its contract, permissions, failure behavior, automated tests, manual evidence where applicable, documentation, migration/deprecation impact and release artifact are all reviewed.

## Getting started

Requires PHP 8.1+ and Composer.

```bash
composer install
composer test    # 24 unit tests covering the risk policy, JSON export, command core, and abilities map
composer lint    # WordPress coding standards (PHPCS)

# Audit a role/capability export (get_editable_roles() shape):
php bin/wp-abilities-auditor fixtures/roles.json            # exits 1 if risks found
php bin/wp-abilities-auditor fixtures/roles.json --format=json
```

## What is built today

- `Policy` (`src/Policy.php`) — data-driven map of capabilities that are dangerous on any
  non-administrator role (code execution, account control, configuration), each with a severity.
- `Auditor` (`src/Auditor.php`) — flags every risky grant, sorted most-severe first; framework-free.
- `Report` (`src/Report.php`) — text and JSON output.
- `AuditExport` (`src/AuditExport.php`) — serializes the audited roles and findings to a stable,
  schema-versioned JSON document (sorted keys, no timestamps) so the output is byte-for-byte
  reproducible and safe to diff, snapshot, or commit as evidence.
- CLI (`bin/wp-abilities-auditor`) — reads a JSON roles export and exits non-zero on findings, so it
  gates CI. Accepts both `["cap"]` and `{"cap": true}` capability shapes.
- `AuditCommand` (`src/AuditCommand.php`) — the WP-CLI command and admin screen. Its real work
  (`normalize_roles()` to accept the `get_editable_roles()` shape, then `run()` to audit, render, and
  pick an exit code) is framework-free and unit-tested; only the WP-CLI/`add_menu_page` registration
  and dispatch are thin glue, guarded by `class_exists`/`function_exists` so the file loads anywhere.
  The standalone `bin` reuses the same core, so there is one audit code path.
- `AbilitiesMap` (`src/AbilitiesMap.php`) — maps the audit model to an Abilities-API-style structure:
  one named, categorised ability descriptor per role/capability grant, annotated with whether the
  audit flagged it and at what severity. Deterministically ordered and framework-free.
- 24 PHPUnit tests; PHPCS/WPCS clean; CI on PHP 8.1 and 8.3.

## Documented boundary (not yet built)

The command core, its exit codes, and the abilities mapping are all built and unit-tested here.
The single step that irreducibly needs a running WordPress is reading the *live* roles via
`get_editable_roles()` inside the WP-CLI dispatch and the admin screen; everything downstream of
that gather step runs and is verified without WordPress. Broader Abilities API coverage follows as
that API stabilises.
