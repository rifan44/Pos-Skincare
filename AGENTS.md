# Codex Instructions — POS Skincare

## Project

Laravel/PHP + MySQL (Laragon), Blade, Bootstrap 5, Vanilla JavaScript, Font Awesome 6, SweetAlert2. Verify exact versions and current implementation from repository files. The project is a skincare-store POS for coursework; repository code and database migrations are the source of truth, not prior AI summaries.

## Engineering rules

- Inspect the relevant code and existing conventions before editing. Never invent class names, routes, fields, schema, relationships, Settings keys, transaction statuses, dependency versions, or formulas.
- Preserve the current architecture and behavior. Prefer focused diffs over rewriting full files. Do not change dependencies, schema, authentication, authorization, or unrelated modules without evidence and explicit approval.
- Never read, copy, or print `.env` contents, credentials, tokens, API keys, or passwords. If configuration must be checked, report only non-secret metadata.
- Do not delete files, deploy, commit/push, alter remote state, run destructive migrations, wipe/truncate tables, or modify an active database without explicit approval.
- Any checkout/stock testing must use an isolated test database. Do not use real transaction data for tests.
- Before a substantial implementation, identify relevant files and the proposed scope. For read-only audit requests, do not write files.
- Run relevant tests/checks when safe and available. Report actual commands and results; never claim a test passed unless it ran successfully.

## UI conventions

Follow the existing `app.blade.php` and the actual Category/Product views first. Target palette: page background `#fdfbf7`, white cards `#ffffff`, text `#3b3331`, default brand `#c78b7d`; retain DB-driven theme colors if implemented. Use Playfair Display for headings and `.font-serif`, Plus Jakarta Sans for body/UI. Avoid duplicating global font/style imports.

- Cards: `border-0 shadow-sm rounded-4`.
- Tables: `table table-hover align-middle`; header: `thead.table-light`.
- Buttons: `rounded-pill`.
- Non-POS page headers: Bootstrap row with `col-sm-8` title/description and `col-sm-4 text-sm-end` actions.
- Align table action buttons using the existing flex/gap pattern and preserve responsive behavior.

## Known hand-off notes

- Reported unfinished views: `resources/views/pages/dashboard/index.blade.php`, `resources/views/pages/settings/index.blade.php`, `resources/views/pages/history/index.blade.php`, `resources/views/pages/users/index.blade.php`.
- Reported old typo: `$totalOmset` versus `$totalOmzet`. Inspect current controller/view naming first; do not make a partial rename. The dashboard task requests `$totalOmset`, `$totalLaba`, `$totalTransaksi`, and `$stokMenipis`, with `$stokMenipis` representing products with stock `< 10`; verify actual schema and report formulas before implementing.
- Dashboard financial values must follow the real transaction schema, current report definition, and transaction status semantics. Do not guess the formula for net profit/HPP.
- Receipt identity (store name, address, footer message) must come from the Settings source/keys actually present in this repository. Discover the actual print route and view path before changing them.
- Stock updates must remain consistent with successful checkout only. Evaluate database transactions, stock validation, and concurrency against the implementation; test only against an isolated database.

## Completion report

For each task, summarize: files inspected, files changed, root cause/reason, behavior preserved, commands/tests actually run and their results, and remaining risks or unverified items. Use the project hand-off document if present for fuller context and task prompts.
