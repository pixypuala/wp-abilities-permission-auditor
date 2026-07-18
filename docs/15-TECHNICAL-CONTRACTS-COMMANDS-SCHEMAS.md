# Technical Contracts, Commands and Schemas

This chapter removes ambiguity before code. Names may change only through an ADR; the implemented README and CLI help must remain synchronized with the accepted contract.

## Required command surface

| Command | Required behavior |
|---|---|
| wp ability-audit doctor | Verify WordPress/Abilities API version, registrations, REST and optional adapter availability. |
| wp ability-audit inventory --format=<table\|json> | Passively list categories, abilities, schemas, permissions and exposure. |
| wp ability-audit check --policy=<file> | Run passive rules and produce findings. |
| wp ability-audit roles --ability=<name> | Evaluate permission callback for disposable role/capability matrix without executing ability. |
| wp ability-audit probe --ability=<name> --mode=isolated | Execute only approved non-destructive probes in disposable environment. |
| wp ability-audit diff <baseline> <current> | Report new/removed/changed abilities, permissions and effect classes. |
| wp ability-audit report <result> --format=<json\|markdown\|sarif> | Render immutable audit result. |
| wp ability-audit explain <rule-id> | Show rationale, evidence requirements, false-positive notes and remediation. |

## Configuration example

```yaml
policyVersion: 1
defaultMode: passive
rules:
  requireInputSchema: error
  requireOutputSchema: warning
  permissionAlwaysTrue: error
  destructiveRemoteExposure: error
exceptions:
  - rule: example-rule
    ability: vendor/example
    reason: <documented reason>
    owner: <owner>
    expires: YYYY-MM-DD
activeProbes:
  allowedEffects: [read]
```

The final implementation must publish a machine-readable JSON Schema, reject unknown/unsafe fields according to policy, report source locations for invalid input, and support `--format=json` for automation where appropriate. Environment variables may provide secrets or CI overrides but cannot silently replace committed project behavior.

## Core data models

- AbilityInventory: name/category/metadata/schemas/callbacks/exposure/version.
- EffectClassification: read/write/destructive/external/credential/personal/admin plus confidence/evidence.
- RolePermissionObservation: role/capabilities/auth context/allowed/error.
- AuditFinding: rule, severity, confidence, ability, evidence, affected roles and remediation.
- PolicyException: rule/ability/reason/owner/approval/expiry.

## API and stability rules

- Passive inventory uses official Abilities API functions/hooks and feature detection.
- Runtime probes are separate, opt-in and refuse unapproved effects.
- Finding/rule IDs are stable; severity changes require changelog and policy migration note.
- MCP/REST exposure is recorded as an exposure path, not proof that an action is exploitable.

## Common exit-code contract

| Code | Meaning | Retry guidance |
|---|---|---|
| 0 | All requested operations completed and required assertions passed | No retry needed |
| 1 | Valid execution found a contract/budget/audit/test failure | Fix product/configuration; blind retry prohibited |
| 2 | Invalid command or configuration | Correct input |
| 3 | Unsupported or missing environment/dependency | Change environment or support policy |
| 4 | Permission or safety policy denied the operation | Do not bypass; obtain correct authorization/environment |
| 5 | Setup, migration or fixture preparation failed | Inspect diagnostics; clean owned state before retry |
| 6 | Timeout, cancellation or external/network failure | Retry only under documented bounded policy |
| 7 | Infrastructure failure unrelated to evaluated product behavior | Retry after environment repair; preserve original evidence |
| 8 | Internal defect/invariant violation | File a bug with redacted diagnostic bundle |

Commands that do not need all codes may use the applicable subset, but meanings cannot conflict.

## Output and logging contract

- Human output goes to stdout; diagnostics/progress to stderr where CLI conventions require machine-readable stdout.
- `--format=json` emits one valid documented schema, no decorative prose.
- Every run prints or records run ID, tool version, source SHA, platform versions, config hash and safety mode.
- Errors contain stable code, path/subject, remediation and redacted context.
- Verbose/debug mode is opt-in and still redacts secrets and personal data.
- Cancellation returns a distinct status and runs ownership-based cleanup.

## Schema evolution

Schemas include `schemaVersion`. Additive optional fields may be backward compatible; required fields, changed meaning/type, renamed IDs and removed enum values are breaking. Readers must reject unsupported major versions clearly. Golden fixtures for every supported schema version remain in tests through the deprecation window.
