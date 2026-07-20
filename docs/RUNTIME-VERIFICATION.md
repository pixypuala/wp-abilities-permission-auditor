# Runtime verification

The command core and the abilities mapping are unit-tested without WordPress.
The one step that cannot be: reading the *live* roles out of a running install
and auditing what they actually grant.

## The run

WordPress 7.0.2, PHP 8.2, with WooCommerce installed — so the role set is a
realistic one rather than a bare default.

```
$ cat load-auditor.php
<?php
require_once '…/wp-abilities-permission-auditor/vendor/autoload.php';
Pixypuala\AbilitiesAuditor\AuditCommand::register();

$ wp --require=load-auditor.php abilities-auditor audit --format=text
1 finding(s):
  [MEDIUM  ] editor -> unfiltered_html

$ wp --require=load-auditor.php abilities-auditor audit --format=json
{
    "findingCount": 1,
    "findings": [
        {
            "role": "editor",
            …
        }
    ]
}
```

Both formats render from the same audited result. The finding is real, not a
fixture: WordPress genuinely grants `unfiltered_html` to the editor role on a
single-site install, which is exactly the kind of quiet capability grant this
tool exists to surface.

## What this establishes

The gather step — `get_editable_roles()` inside the WP-CLI dispatch — works
against a live install, the audit runs over the real role set, and both output
formats serialise correctly. Everything downstream of the gather was already
proven by the unit suite.

## What is still not proven here

Broader Abilities API coverage, which follows as that API stabilises, and the
admin screen, which is registered but has not been driven through a browser.
