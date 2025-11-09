# Feature Management Quick Start Guide

A quick reference for working with the feature management system.

## Creating a New Feature

### 1. Find the Next Number

```bash
# List all features to see the last number
ls -1 docs/features/*.md | sort -V | tail -1
```

### 2. Create the Feature File

```bash
# Use the next sequential number
touch docs/features/[NUMBER]-[feature-name].md
```

### 3. Copy the Template

Use the template from `docs/features/README.md` or copy from an existing feature file.

### 4. Fill in the Details

```markdown
# [Number] - [Feature Name]

**Status:** ⏳ Planned
**Priority:** [Critical|High|Medium|Low]
**Series:** [Series Number] - [Series Name]
**Dependencies:** [List feature numbers]

---

## Overview
[Brief description]

## Business Context
[Why this feature is needed]

## Objectives
- Objective 1
- Objective 2

---

## Tasks
### 1. [Category]
- [ ] Task 1
- [ ] Task 2

---

## Acceptance Criteria
- [ ] Criterion 1
- [ ] Criterion 2

---

## Example Usage
```php
// Code example
```

---

## Related Features
- **[Number] - [Name]:** Description

---

**Created:** [Date]
**Completed:** [Date or "Planned"]
**Actual Effort:** [Time]
```

## Updating a Feature

### Starting Work

1. Change status to `🚧 In Progress`
2. Update the file
3. Commit: `git commit -m "feat([NUMBER]): Start implementation"`

### During Work

1. Check off tasks as completed: `- [x] Task name`
2. Commit regularly: `git commit -m "feat([NUMBER]): Implement X"`

### Completing Work

1. Change status to `✅ Completed`
2. Add Implementation Addendum section
3. Fill in completion date and actual effort
4. Commit: `git commit -m "feat([NUMBER]): Complete feature"`

## Status Emojis

Copy and paste these:

- ⏳ Planned
- 🚧 In Progress
- ✅ Completed
- 🔄 Revised
- ⏸️ On Hold
- ❌ Cancelled

## Series Numbers

- **1000-1999:** Core Utilities
- **2000-2999:** Testing & Quality
- **3000-3999:** Documentation
- **4000-4999:** Infrastructure
- **5000-5999:** Performance
- **6000-6999:** Future Enhancements

## Common Commands

### List All Features

```bash
ls -1 docs/features/*.md | grep -E '^[0-9]'
```

### Find Features by Status

```bash
# Completed
grep -l "Status.*✅ Completed" docs/features/*.md

# In Progress
grep -l "Status.*🚧 In Progress" docs/features/*.md

# Planned
grep -l "Status.*⏳ Planned" docs/features/*.md
```

### Find Features by Priority

```bash
grep -l "Priority.*Critical" docs/features/*.md
```

### Count Features

```bash
# Total
ls -1 docs/features/[0-9]*.md | wc -l

# By status
echo "Completed: $(grep -l "Status.*✅" docs/features/*.md | wc -l)"
echo "In Progress: $(grep -l "Status.*🚧" docs/features/*.md | wc -l)"
echo "Planned: $(grep -l "Status.*⏳" docs/features/*.md | wc -l)"
```

## Git Commit Conventions

### Feature Work

```bash
git commit -m "feat(1000): Implement array key conversion"
git commit -m "test(1000): Add unit tests"
git commit -m "docs(1000): Update API documentation"
git commit -m "fix(1000): Fix edge case in recursive conversion"
```

### Feature Management

```bash
git commit -m "feat: Add feature 1007 - New Utility"
git commit -m "feat: Complete feature 1000"
git commit -m "docs: Update feature 1000 addendum"
```

## Quick Reference

### Feature File Sections

1. **Header** - Number, status, priority, dependencies
2. **Overview** - What it is
3. **Business Context** - Why it's needed
4. **Objectives** - What success looks like
5. **Tasks** - What needs to be done
6. **Acceptance Criteria** - How to verify completion
7. **Example Usage** - Code examples
8. **Implementation Addendum** - What was actually done (after completion)
9. **Related Features** - Dependencies and related work
10. **Footer** - Dates and effort

### Important Rules

✅ **Always:**
- Use the next sequential number
- Follow the template
- Check off tasks as you complete them
- Add implementation details to addendum
- Keep original spec frozen after completion

❌ **Never:**
- Modify completed feature specs
- Skip or reuse numbers
- Delete features
- Change objectives after completion

## Example Workflow

### Day 1: Create Feature

```bash
# Create feature file
touch docs/features/1007-new-feature.md

# Fill in template
# ... edit file ...

# Commit
git add docs/features/1007-new-feature.md
git commit -m "feat: Add feature 1007 - New Feature"
```

### Day 2: Start Work

```bash
# Update status to In Progress
# ... edit file ...

# Commit
git commit -m "feat(1007): Start implementation"

# Do work, commit regularly
git commit -m "feat(1007): Implement core functionality"
git commit -m "test(1007): Add unit tests"
```

### Day 3: Complete

```bash
# Update status to Completed
# Fill in Implementation Addendum
# ... edit file ...

# Commit
git commit -m "feat(1007): Complete feature"
```

## Need Help?

- **Full Documentation:** `docs/features/README.md`
- **Feature Index:** `docs/features/INDEX.md`
- **General Plan:** `docs/GENERAL-PLAN.md`
- **Setup Summary:** `docs/FEATURE-SYSTEM-SETUP.md`

---

**Quick Tip:** Bookmark this file for easy reference!
