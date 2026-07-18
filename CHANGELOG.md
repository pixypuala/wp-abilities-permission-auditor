# Changelog

All notable changes to this project are documented here. The format is based on
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and this project adheres
to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Repository scaffolding: governance files, docs, and CI skeleton.
- Risk policy + Auditor: flags dangerous capabilities on non-administrator roles, severity-ranked.
- Text/JSON Report and a CLI that gates CI (exit 1 on findings).
- 7 PHPUnit tests; PHPCS/WPCS clean; CI on PHP 8.1 and 8.3.
