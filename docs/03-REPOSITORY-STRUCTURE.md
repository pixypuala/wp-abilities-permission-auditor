# Repository Structure

## Required root

```text
wp-abilities-permission-auditor/
├── .github/
│   ├── ISSUE_TEMPLATE/
│   ├── workflows/
│   │   ├── ci.yml
│   │   ├── scheduled-compatibility.yml
│   │   ├── security.yml
│   │   └── release.yml
│   ├── CODEOWNERS
│   ├── dependabot.yml
│   └── PULL_REQUEST_TEMPLATE.md
├── docs/
│   ├── adr/
│   ├── architecture/
│   ├── api/
│   ├── contributor/
│   ├── evidence/
│   ├── operations/
│   ├── security/
│   └── user/
├── packages-or-components/
│   ├── packages/wp-plugin        # Runtime inventory, hooks, safe probes and admin/CLI integration.
│   ├── packages/audit-core        # Rules, taxonomy, findings, policy and diff engine.
│   ├── packages/reporters        # JSON, Markdown and SARIF output.
│   ├── fixtures/safe        # Well-scoped ability examples.
│   ├── fixtures/vulnerable        # Overbroad, missing schema, sensitive and destructive examples.
│   ├── policies        # Baseline rule sets and severity rationale.
│   ├── docs/mcp        # Adapter-specific exposure and threat guidance.
├── fixtures/
│   ├── known-good/
│   └── known-bad/
├── schemas/
├── scripts/
├── tests/
│   ├── unit/
│   ├── integration/
│   ├── contract/
│   ├── e2e/
│   ├── security/
│   └── performance/
├── tools/
├── CHANGELOG.md
├── CODE_OF_CONDUCT.md
├── CONTRIBUTING.md
├── LICENSE
├── README.md
├── SECURITY.md
├── SUPPORT.md
└── package/composer/tool configuration and lockfiles
```

## Root-file responsibilities

- `README.md`: product, quick start, bounded support statement, demo, limitations and links.
- `CONTRIBUTING.md`: environment, commands, branch/commit/PR rules, tests, generated files and review expectations.
- `SECURITY.md`: supported versions, private reporting route, response expectations and disclosure coordination.
- `SUPPORT.md`: supported questions, issue routing, response policy and what is not support.
- `CHANGELOG.md`: user-visible change history; generated commit dumps are not acceptable.
- `LICENSE`: approved open-source license after dependency/asset compatibility review.
- Lockfiles: one authoritative lockfile per ecosystem; never hand-edit.

## Naming and code rules

- One domain concept per file/class/module where practical.
- Names describe responsibility, not implementation accident (`ContractRunner`, not `Helper2`).
- Tests mirror source paths and name behavior/expected result.
- Fixtures are immutable inputs; generated runtime state belongs in ignored temporary directories.
- Never commit `.env`, credentials, production exports, user uploads, browser storage or vendor caches.
- Generated artifacts include a header or manifest naming the generator and source hash.

## Ownership

CODEOWNERS must cover security-sensitive code, public schemas, release workflows, migrations and dependency manifests. At least two maintainers review changes to release/security workflows after the project has more than one maintainer.
