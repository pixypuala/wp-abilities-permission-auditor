# Changelog

All notable changes to this project are documented here. The format is based on
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and this project adheres
to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Repository scaffolding: governance files, docs, and CI skeleton.
- Risk policy + Auditor: flags dangerous capabilities on non-administrator roles, severity-ranked.
- Text/JSON Report and a CLI that gates CI (exit 1 on findings).
- `AuditExport`: deterministic, schema-versioned JSON serializer (sorted role keys and
  capabilities, no timestamps) for reproducible, diffable audit evidence.
- `AuditCommand` + `AuditResult`: the WP-CLI command and admin screen, with their real work
  (`normalize_roles()` accepting the `get_editable_roles()` shape, then `run()` to audit, render,
  and choose an exit code) extracted into framework-free, unit-tested methods; only WP-CLI and
  `add_menu_page` registration remain as thin, guarded glue. The standalone `bin` now reuses this
  core so there is a single audit code path.
- `AbilitiesMap`: framework-free mapper from the audit model to an Abilities-API-style structure
  (one named, categorised ability descriptor per role/capability grant, severity-annotated,
  deterministically ordered).
- 24 PHPUnit tests; PHPCS/WPCS clean; CI on PHP 8.1 and 8.3.
