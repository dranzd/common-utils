# Feature Management System

## Overview

This directory contains feature specifications for the common-utils library. Each feature is documented in a separate markdown file with a unique numeric identifier.

## Feature Numbering System

Features are numbered sequentially starting from **1000**, with increments of 1 for each new feature:

- **1000** - First feature
- **1001** - Second feature
- **1002** - Third feature
- And so on...

### Numbering Series

Features are organized into series based on their category:

- **1000-1999**: Core Utilities (ArrayUtil, StringUtil, Idx, etc.)
- **2000-2999**: Testing & Quality Assurance
- **3000-3999**: Documentation & Examples
- **4000-4999**: Development Tools & Infrastructure
- **5000-5999**: Performance & Optimization
- **6000-6999**: Future Enhancements

## Feature File Naming Convention

Each feature file follows this naming pattern:

```
[number]-[kebab-case-description].md
```

**Examples:**
- `1000-array-util-core.md`
- `1001-string-util-core.md`
- `1002-idx-safe-access.md`
- `2000-unit-test-coverage.md`

## Feature File Template

Each feature file must follow this structure:

```markdown
# [Number] - [Feature Name]

**Status:** [Status Emoji] [Status Text]
**Priority:** [Critical|High|Medium|Low]
**Series:** [Series Number] - [Series Name]
**Dependencies:** [List of feature numbers this depends on]

---

## Overview

Brief description of what this feature provides and why it's important.

## Business Context

Explanation of the business or technical need this feature addresses.

## Objectives

- Clear, measurable objectives
- What success looks like
- Key deliverables

---

## Tasks

### 1. [Task Category]

- [ ] Task item 1
  - [ ] Sub-task if needed
- [ ] Task item 2
- [ ] Task item 3

### 2. [Another Task Category]

- [ ] Task item 1
- [ ] Task item 2

---

## Acceptance Criteria

- [ ] Criterion 1
- [ ] Criterion 2
- [ ] Criterion 3
- [ ] Test coverage ≥ 80%
- [ ] Documentation complete

---

## Example Usage

```php
<?php
// Code examples showing how to use the feature
```

---

## Implementation Addendum

**Date:** [Date]
**Status:** [Status]

### What Was Implemented

Description of what was actually built, including any deviations from the original plan.

### Files Created/Modified

**Created:**
- path/to/file1.php
- path/to/file2.php

**Modified:**
- path/to/existing/file.php

### Key Features

- Feature highlight 1
- Feature highlight 2

### Design Decisions

1. **Decision 1:** Rationale
2. **Decision 2:** Rationale

### Next Steps

- Related work that should follow
- Integration points
- Future enhancements

---

## Related Features

- **[Number] - [Feature Name]:** Relationship description
- **[Number] - [Feature Name]:** Relationship description

---

**Created:** [Date]
**Completed:** [Date or "In Progress"]
**Actual Effort:** [Time spent]
```

## Status Values

Features can have the following statuses:

- **⏳ Planned** - Feature is planned but not started
- **🚧 In Progress** - Feature is currently being implemented
- **✅ Completed** - Feature is fully implemented and tested
- **🔄 Revised** - Feature was revised after initial completion
- **⏸️ On Hold** - Feature implementation is paused
- **❌ Cancelled** - Feature was cancelled

## Priority Levels

- **Critical** - Must be implemented immediately, blocks other work
- **High** - Important feature, should be implemented soon
- **Medium** - Standard priority, implement in normal workflow
- **Low** - Nice to have, can be deferred

## Feature Lifecycle

### 1. Creation

When a new feature is identified:

1. Assign the next available feature number
2. Create a new markdown file using the naming convention
3. Fill in the template with all known information
4. Set status to **⏳ Planned**
5. Identify dependencies on other features
6. Assign priority level

### 2. Implementation

When work begins on a feature:

1. Update status to **🚧 In Progress**
2. Check off tasks as they are completed
3. Update the file with any new information or changes
4. Document design decisions as they are made

### 3. Completion

When a feature is finished:

1. Update status to **✅ Completed**
2. Fill in the **Implementation Addendum** section
3. Document all files created/modified
4. Add completion date and actual effort
5. **DO NOT modify the original feature specification**
6. Any changes or updates go in the addendum only

### 4. Updates After Completion

**IMPORTANT:** Once a feature is marked as **✅ Completed**, the original specification is frozen.

- ✅ **DO:** Add information to the Implementation Addendum
- ✅ **DO:** Create new related features for additional work
- ✅ **DO:** Update the status if work resumes
- ❌ **DO NOT:** Modify the original objectives or tasks
- ❌ **DO NOT:** Remove or change the original specification

If significant changes are needed after completion:

1. Add a new dated section to the Implementation Addendum
2. Document what changed and why
3. Reference any new features created as a result

### 5. Tracking Progress

Check off tasks using markdown checkboxes:

```markdown
- [x] Completed task
- [ ] Incomplete task
```

## Feature Dependencies

When a feature depends on another:

1. List the dependency in the **Dependencies** field
2. Use the format: `[number] ([Feature Name])`
3. Example: `1000 (Array Util Core), 1001 (String Util Core)`

Dependencies should be completed before starting dependent features.

## Searching Features

### By Status

```bash
# Find all completed features
grep -r "Status.*✅ Completed" docs/features/

# Find features in progress
grep -r "Status.*🚧 In Progress" docs/features/
```

### By Priority

```bash
# Find critical features
grep -r "Priority.*Critical" docs/features/
```

### By Series

```bash
# Find all core utility features (1000-1999)
ls docs/features/1[0-9][0-9][0-9]-*.md
```

## Best Practices

### Writing Features

1. **Be Specific:** Clear, measurable objectives
2. **Be Realistic:** Achievable tasks and timelines
3. **Be Complete:** Include all necessary information
4. **Be Consistent:** Follow the template structure
5. **Be Detailed:** Provide context and rationale

### Managing Features

1. **One Feature, One File:** Each feature gets its own file
2. **Sequential Numbering:** Use the next available number
3. **Descriptive Names:** Use clear, kebab-case descriptions
4. **Regular Updates:** Keep status current
5. **Document Everything:** Capture decisions and changes

### Implementation

1. **Check Dependencies:** Ensure dependencies are completed
2. **Update Status:** Keep status current as work progresses
3. **Document Changes:** Add to addendum, don't modify original
4. **Link Related Work:** Reference related features
5. **Preserve History:** Never delete completed features

## Example Workflow

### Creating a New Feature

```bash
# 1. Find the next available number
ls docs/features/ | sort -n | tail -1
# Last file: 1005-something.md

# 2. Create new feature file
# Next number: 1006
touch docs/features/1006-new-feature-name.md

# 3. Copy template and fill in details
# 4. Commit the new feature file
git add docs/features/1006-new-feature-name.md
git commit -m "feat: Add feature 1006 - New Feature Name"
```

### Implementing a Feature

```bash
# 1. Update status to In Progress
# 2. Work on implementation
# 3. Check off tasks as completed
# 4. Commit changes regularly
git add docs/features/1006-new-feature-name.md
git commit -m "feat: Update feature 1006 progress"
```

### Completing a Feature

```bash
# 1. Update status to Completed
# 2. Fill in Implementation Addendum
# 3. Add completion date
# 4. Commit final update
git add docs/features/1006-new-feature-name.md
git commit -m "feat: Complete feature 1006 - New Feature Name"
```

## Integration with Development Workflow

### Git Commit Messages

Reference features in commit messages:

```bash
git commit -m "feat(1006): Implement core functionality"
git commit -m "test(1006): Add unit tests"
git commit -m "docs(1006): Update API documentation"
```

### Pull Requests

Link to features in PR descriptions:

```markdown
## Related Features

- Implements #1006 - New Feature Name
- Depends on #1005 - Previous Feature
```

### Issue Tracking

Create GitHub issues that reference features:

```markdown
Title: Implement Feature 1006 - New Feature Name

Description:
This issue tracks the implementation of feature 1006.

See: docs/features/1006-new-feature-name.md
```

## Reporting

### Feature Status Report

Generate a status report:

```bash
# Count features by status
echo "Completed: $(grep -r "Status.*✅ Completed" docs/features/ | wc -l)"
echo "In Progress: $(grep -r "Status.*🚧 In Progress" docs/features/ | wc -l)"
echo "Planned: $(grep -r "Status.*⏳ Planned" docs/features/ | wc -l)"
```

### Feature List

Generate a feature list:

```bash
# List all features with status
for file in docs/features/*.md; do
  number=$(basename "$file" | cut -d'-' -f1)
  name=$(grep "^# " "$file" | head -1 | sed 's/# //')
  status=$(grep "Status:" "$file" | head -1)
  echo "$number: $name - $status"
done
```

## Maintenance

### Archiving Old Features

Features are never deleted, but very old completed features can be moved to an archive:

```bash
# Create archive directory
mkdir -p docs/features/archive/

# Move old features (example: features from 2023)
mv docs/features/1000-*.md docs/features/archive/
```

### Renumbering

**DO NOT renumber features.** Feature numbers are permanent identifiers. If you need to reorganize:

1. Create new features with new numbers
2. Mark old features as superseded in the addendum
3. Link to the new feature

---

**Version:** 1.0
**Created:** November 9, 2025
**Last Updated:** November 9, 2025
