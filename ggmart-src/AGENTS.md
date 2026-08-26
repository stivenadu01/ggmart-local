# GG-Mart Coding Rules

## Stack

- PHP Native
- MySQL
- Tailwind CSS
- Alpine.js
- Chart.js

## General

- Audit existing implementation before making changes.
- Do not rewrite working features unnecessarily.
- Preserve existing business logic unless explicitly instructed otherwise.
- Do not create dummy data.
- Prefer simple, maintainable code over unnecessary abstractions.
- Do not duplicate existing functionality.

## Frontend

- `input.css` is the central design system.
- Use reusable utilities/components from `input.css`.
- Avoid duplicate Tailwind utility classes.
- Before creating a new utility, check whether an existing utility can be reused.
- Do not create duplicate CSS.
- Avoid unnecessary inline styles.
- Keep UI responsive for desktop, tablet, and mobile.
- Preserve the existing Admin Shell unless explicitly instructed otherwise.
- Do not introduce unnecessary global scroll/layout behavior.
- Use clear labels, realistic placeholders, helper text, and appropriate UI states.
- Destructive actions require confirmation.
- Loading/disabled states should be provided where appropriate.
- Keep action buttons consistent in size and alignment.

## Backend

- Do not change business logic without explicit instruction.
- Do not change API contracts without a valid reason.
- Do not change database schema without explicit instruction.
- Preserve existing validation and authorization.
- Preserve FIFO logic.
- Preserve HPP logic.
- Preserve stock/batch/mutation logic.
- Preserve transaction logic.
- Preserve existing role permissions unless explicitly instructed.

## Roles

The application has exactly three roles:

- pelanggan
- admin
- pimpinan

Do not introduce or rename roles without explicit instruction.

## Code Efficiency

- Reuse existing helpers, components, utilities, and functions.
- Do not duplicate Alpine.js functions.
- Do not duplicate API calls.
- Do not register duplicate event listeners.
- Do not introduce unnecessary polling.
- Prefer centralized reusable logic over repeated code.

## Workflow

Before changing code:

1. Inspect the relevant existing implementation.
2. Identify dependencies and existing reusable components.
3. Check `input.css` before adding CSS.
4. Check existing API/controller/model logic before modifying backend.
5. Make the smallest necessary change.

After changing code:

1. Run PHP syntax checks on modified PHP files.
2. Run JavaScript syntax checks on modified JavaScript files.
3. Check for accidental API/route changes.
4. Check for duplicate CSS/utilities.
5. Report files changed.
6. Report validation results.
7. Explain any backend/database changes.
