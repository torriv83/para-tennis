---
name: code-review
description: Use when reviewing code, performing code review, checking code quality, or when asked to review changes. Provides structured review focusing on security, performance, and Laravel best practices.
---

# Code Review Skill

When reviewing code, follow this structured approach for actionable feedback.

## Review Checklist

### Security
- [ ] SQL injection risks (raw queries, user input in queries)
- [ ] Mass assignment protection (`$fillable` / `$guarded`)
- [ ] Authorization checks (policies, gates, `authorize()`)
- [ ] XSS vulnerabilities (unescaped output in Blade)
- [ ] CSRF protection on forms
- [ ] Sensitive data exposure in logs/responses

### Performance
- [ ] N+1 query problems (missing eager loading)
- [ ] Unnecessary database queries in loops
- [ ] Missing indexes on frequently queried columns
- [ ] Large dataset handling (chunking, lazy collections)

### Laravel Conventions
- [ ] Form Requests for validation (not inline)
- [ ] Proper use of Eloquent relationships
- [ ] Route model binding where applicable
- [ ] Config/env usage (`config()` not `env()` outside config files)
- [ ] Appropriate use of queued jobs for heavy tasks

### Code Quality
- [ ] Single responsibility principle
- [ ] Meaningful variable/method names
- [ ] No dead/commented code
- [ ] Proper error handling
- [ ] Test coverage for new functionality

## Review Output Format

When reviewing, provide:

1. **Critical Issues** - Must fix (security, bugs)
2. **Improvements** - Should fix (performance, conventions)
3. **Suggestions** - Nice to have (style, minor enhancements)

Be specific: reference file:line and explain WHY something is an issue, not just WHAT.
