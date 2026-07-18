# Product Definition Using PCAAP

## Problem

Discoverable executable abilities can be misclassified, over-permissioned, poorly described, unsafe for remote use, or inconsistent with the capabilities and data they touch.

## Cost

- confused-deputy behavior
- unauthorized state changes
- sensitive-data exposure
- unsafe automation
- false trust in metadata that is not tested

## Answer

Inventory registered abilities, validate metadata/input/output schemas, trace permission callbacks, classify effects and data sensitivity, test roles, compare policies, and emit JSON/Markdown/SARIF findings.

## Advantage

The project addresses a new WordPress surface with explicit scope, version guards and reproducible vulnerable fixtures instead of AI security marketing claims.

## Proof

- detects intentionally over-broad permission callback
- role/capability execution matrix
- REST and WP-CLI exposure inventory
- read/write/destructive/external side-effect classification
- MCP-adapter risk guidance with no automatic trust

## Ask

Audit one real ability registration, contribute a safe/vulnerable fixture, or review the classification taxonomy.

## Product principles

1. **Bound every claim.** State exactly which versions, environments, contracts, roles, journeys and evidence support a claim.
2. **Prefer official platform APIs.** Private internals may be studied but must not become undocumented production dependencies.
3. **Prove failure detection.** Every important gate needs a known-bad fixture or mutation proving that it can fail.
4. **Local equals CI.** CI invokes versioned repository commands; it does not contain hidden logic unavailable to contributors.
5. **Safe by default.** Destructive, privileged, remote, secret-bearing or production-targeting behavior requires explicit opt-in.
6. **Documentation is a product surface.** A new contributor must be able to install, reproduce, test and understand limitations without private guidance.
7. **Maintenance is designed before launch.** Compatibility policy, ownership, deprecation, security disclosure and archive criteria exist before v1.0.

## Success outcomes

- A qualified developer can reach the documented demo from a clean clone without guessing.
- A reviewer can map every user-facing promise to code, tests and evidence.
- A maintainer can identify the supported versions, release process and breaking-change policy.
- A security reviewer can find permissions, sensitive data, network access and unsafe operations in one threat model.
- An outside contributor can select a scoped issue, run checks locally and submit a compliant pull request.

## Failure conditions

The project is not ready when it depends on undocumented local services, hides secrets in examples, uses vague compatibility language, lacks negative tests, has unowned critical code, cannot produce release artifacts from a tag, or has no plan for security reports and breaking changes.
