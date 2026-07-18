# Implementation Backlog

This backlog is the minimum decomposition, not a substitute for issue-specific design. Each issue must include owner, dependencies, security/compatibility/docs impact, test plan and evidence link. Split issues that cannot be reviewed safely in one pull request.

| ID | Work item | Acceptance | Initial status |
|---|---|---|---|
| ISSUE-001 | Bootstrap | Initialize license, governance, CODEOWNERS, security and support files. | Not started |
| ISSUE-002 | Architecture | Accept repository topology, support, schema, environment, trust and test ADRs. | Not started |
| ISSUE-003 | Tooling | Create version files, authoritative lockfiles and immutable installation commands. | Not started |
| ISSUE-004 | Doctor | Implement read-only environment diagnostics and remediation output. | Not started |
| ISSUE-005 | Fixture | Create smallest deterministic known-good fixture and cleanup ownership. | Not started |
| ISSUE-006 | Failure fixture | Create first known-bad fixture and prove the intended gate fails. | Not started |
| ISSUE-007 | Static quality | Configure formatting, lint, types/static analysis, schema and generated-file drift checks. | Not started |
| ISSUE-008 | Integration environment | Create disposable WordPress/database/browser lifecycle with cleanup. | Not started |
| ISSUE-009 | Security | Complete threat model and add permission/input/network/filesystem/redaction tests. | Not started |
| ISSUE-010 | Evidence | Define immutable result/evidence directory, manifest and redaction inspection. | Not started |
| ISSUE-011 | CI | Implement PR target cell using only repository-owned commands. | Not started |
| ISSUE-012 | Scheduled CI | Implement sampled matrix, next-beta checks and maintenance health. | Not started |
| ISSUE-013 | Release | Implement protected tag build, artifact inspection/checksum and artifact-install smoke. | Not started |
| ISSUE-014 | Docs | Verify clean-clone tutorial through an uninvolved reviewer. | Not started |
| ISSUE-015 | Compatibility | Publish dated tested/unsupported matrix tied to release SHA. | Not started |
| ISSUE-016 | Upgrade | Create previous-release fixture and candidate upgrade/recovery test. | Not started |
| ISSUE-017 | CLI: implement `wp ability-audit doctor` | Verify WordPress/Abilities API version, registrations, REST and optional adapter availability. Includes unit/contract tests, help text, JSON behavior where applicable, and failure cases. | Not started |
| ISSUE-018 | CLI: implement `wp ability-audit inventory --format=<table\|json>` | Passively list categories, abilities, schemas, permissions and exposure. Includes unit/contract tests, help text, JSON behavior where applicable, and failure cases. | Not started |
| ISSUE-019 | CLI: implement `wp ability-audit check --policy=<file>` | Run passive rules and produce findings. Includes unit/contract tests, help text, JSON behavior where applicable, and failure cases. | Not started |
| ISSUE-020 | CLI: implement `wp ability-audit roles --ability=<name>` | Evaluate permission callback for disposable role/capability matrix without executing ability. Includes unit/contract tests, help text, JSON behavior where applicable, and failure cases. | Not started |
| ISSUE-021 | CLI: implement `wp ability-audit probe --ability=<name> --mode=isolated` | Execute only approved non-destructive probes in disposable environment. Includes unit/contract tests, help text, JSON behavior where applicable, and failure cases. | Not started |
| ISSUE-022 | CLI: implement `wp ability-audit diff <baseline> <current>` | Report new/removed/changed abilities, permissions and effect classes. Includes unit/contract tests, help text, JSON behavior where applicable, and failure cases. | Not started |
| ISSUE-023 | CLI: implement `wp ability-audit report <result> --format=<json\|markdown\|sarif>` | Render immutable audit result. Includes unit/contract tests, help text, JSON behavior where applicable, and failure cases. | Not started |
| ISSUE-024 | CLI: implement `wp ability-audit explain <rule-id>` | Show rationale, evidence requirements, false-positive notes and remediation. Includes unit/contract tests, help text, JSON behavior where applicable, and failure cases. | Not started |
| ISSUE-025 | Domain: implement `AbilityInventory` model | AbilityInventory: name/category/metadata/schemas/callbacks/exposure/version. Validate serialization, invariants and backward compatibility. | Not started |
| ISSUE-026 | Domain: implement `EffectClassification` model | EffectClassification: read/write/destructive/external/credential/personal/admin plus confidence/evidence. Validate serialization, invariants and backward compatibility. | Not started |
| ISSUE-027 | Domain: implement `RolePermissionObservation` model | RolePermissionObservation: role/capabilities/auth context/allowed/error. Validate serialization, invariants and backward compatibility. | Not started |
| ISSUE-028 | Domain: implement `AuditFinding` model | AuditFinding: rule, severity, confidence, ability, evidence, affected roles and remediation. Validate serialization, invariants and backward compatibility. | Not started |
| ISSUE-029 | Domain: implement `PolicyException` model | PolicyException: rule/ability/reason/owner/approval/expiry. Validate serialization, invariants and backward compatibility. | Not started |
| ISSUE-030 | Contract: enforce public API rule | Passive inventory uses official Abilities API functions/hooks and feature detection. Add a contract test and documentation link. | Not started |
| ISSUE-031 | Contract: enforce public API rule | Runtime probes are separate, opt-in and refuse unapproved effects. Add a contract test and documentation link. | Not started |
| ISSUE-032 | Contract: enforce public API rule | Finding/rule IDs are stable; severity changes require changelog and policy migration note. Add a contract test and documentation link. | Not started |
| ISSUE-033 | Contract: enforce public API rule | MCP/REST exposure is recorded as an exposure path, not proof that an action is exploitable. Add a contract test and documentation link. | Not started |

## Backlog execution rules

- Complete Bootstrap through Failure fixture before parallel feature expansion.
- Public contracts and schemas require ADR/API-owner review.
- Security-sensitive and release-workflow issues require designated owner review.
- A CLI/model issue is not complete until error and negative paths are tested.
- Documentation follows the real command/artifact; never document a command that has not been run from a clean clone.
- Close an issue only with linked PR, tests and evidence; administrative closure states why it is no longer needed.
