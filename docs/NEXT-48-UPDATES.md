# Next 48 Updates — wp-abilities-permission-auditor

## Why this file exists

This is a sequenced, honest backlog of at least 48 planned updates that keeps the repository genuinely active over time. Each item is a real unit of work (one issue or pull request) that advances capability, testing, security, documentation, or maintenance — not artificial busywork. Items are ordered so that early work unblocks later work, and grouped into six release milestones. Nothing here is claimed as already done: this is the forward plan.

## How to use it

Convert each checkbox into a tracked issue, attach it to the matching milestone, and close it with the pull request that satisfies it. Aim for a steady cadence (for example one to three items per week) so commit history, releases, and changelog entries reflect continuous, verifiable progress. Re-open or add items whenever revalidation, upstream releases, or user reports surface new work.

Total planned updates: **48** across **6** milestones.

## M1 — v0.1 Foundations & scaffolding

- [ ] **#01** Scaffold the auditor plugin/CLI with a bootstrap guard
- [ ] **#02** Define the inventory model for Abilities API registrations
- [ ] **#03** Set up a dev environment with sample ability registrations
- [ ] **#04** Add coding standards, static analysis, and pre-commit hooks
- [ ] **#05** Create ADRs: defensive-only scope and version guards
- [ ] **#06** Add CI running the auditor against sample fixtures
- [ ] **#07** Implement discovery of registered abilities and their metadata
- [ ] **#08** Add structured JSON output with clear exit codes

## M2 — v0.2 Core capability

- [ ] **#09** Detect abilities exposed to automation/MCP surfaces
- [ ] **#10** Add least-privilege analysis flagging over-broad capabilities
- [ ] **#11** Implement version guards for the Abilities API surface
- [ ] **#12** Add a baseline/allowlist so known-good exposure passes
- [ ] **#13** Detect abilities lacking capability checks
- [ ] **#14** Add a diff mode comparing runs to catch new exposure
- [ ] **#15** Generate a Markdown audit report
- [ ] **#16** Add a GitHub Action wrapper

## M3 — v0.3 Testing, evidence & negative proof

- [ ] **#17** Add self-tests: safe registration passes, risky one fails
- [ ] **#18** Add a known-bad fixture: an ability missing a capability check is flagged
- [ ] **#19** Add integration tests against a sample plugin's abilities
- [ ] **#20** Add golden-file tests for report formats
- [ ] **#21** Add tests for the version-guard behavior
- [ ] **#22** Create an evidence index mapping each check to a test
- [ ] **#23** Add a coverage gate for the analysis core
- [ ] **#24** Add performance checks for large ability inventories

## M4 — v0.4 Security, compatibility & performance

- [ ] **#25** Threat-model ability exposure to automation and MCP clients
- [ ] **#26** Ensure the auditor never itself escalates privilege
- [ ] **#27** Ensure no site data or secrets appear in reports
- [ ] **#28** Add a WordPress/PHP support matrix and test the floor
- [ ] **#29** Add supply-chain scanning
- [ ] **#30** Add observability for new-exposure trends over time
- [ ] **#31** Document rollback for a bad auditor release
- [ ] **#32** Add signed artifacts and checksums

## M5 — v0.5 Documentation, DX & adoption

- [ ] **#33** Write a case study finding an over-privileged ability
- [ ] **#34** Record a demo and reproducible Playground blueprint
- [ ] **#35** Publish the check-authoring guide for reviewers
- [ ] **#36** Document the report format and severity model
- [ ] **#37** Add architecture docs for discovery and analysis
- [ ] **#38** Write a CI-integration guide for the Action
- [ ] **#39** Document how to define an allowlist safely
- [ ] **#40** Add a troubleshooting guide for false positives

## M6 — v1.0+ Community, release cadence & maintenance

- [ ] **#41** Adopt semantic versioning and a maintained changelog
- [ ] **#42** Add protected-tag release automation with evidence
- [ ] **#43** Set a cadence to revalidate against Abilities API changes
- [ ] **#44** Add a quarterly check-set review to the roadmap
- [ ] **#45** Publish a deprecation policy for check changes
- [ ] **#46** Triage issues with documented labels and SLAs
- [ ] **#47** Add 'good first issue' new-check tasks
- [ ] **#48** Schedule recurring dependency and API-surface reviews

## Honesty note

These updates are planned, not completed. They do not assert the software is already built, adopted, certified, bug-free, or secure in every environment. They describe the intended, testable path of work and the cadence for keeping the repository maintained.
