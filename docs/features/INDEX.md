# Feature Index

This document provides a quick reference to all features in the common-utils library.

## Series 1000: Core Utilities

| Feature | Name | Status | Priority |
|---------|------|--------|----------|
| 1000 | ArrayUtil Core Implementation | ✅ Completed | Critical |
| 1001 | StringUtil Core Implementation | ✅ Completed | Critical |
| 1002 | Idx Safe Access Implementation | ✅ Completed | Critical |
| 1003 | DateUtil Core Implementation | 🚧 In Progress | High |
| 1004 | MoneyUtil Core Implementation | 🚧 In Progress | High |
| 1005 | ObjectUtil Core Implementation | ✅ Completed | High |
| 1006 | SearchUtil Core Implementation | 🚧 In Progress | Medium |

## Series 2000: Testing & Quality Assurance

| Feature | Name | Status | Priority |
|---------|------|--------|----------|
| 2000 | Unit Test Coverage for All Utilities | ⏳ Planned | Critical |
| 2001 | PHPStan Level 8+ Compliance | ⏳ Planned | High |

## Series 3000: Documentation & Examples

| Feature | Name | Status | Priority |
|---------|------|--------|----------|
| 3000 | Complete API Documentation | 🚧 In Progress | High |

## Series 4000: Development Tools & Infrastructure

| Feature | Name | Status | Priority |
|---------|------|--------|----------|
| 4000 | GitHub Actions CI/CD Pipeline | ⏳ Planned | High |

## Status Legend

- ✅ **Completed** - Feature is fully implemented and tested
- 🚧 **In Progress** - Feature is currently being implemented
- ⏳ **Planned** - Feature is planned but not started
- 🔄 **Revised** - Feature was revised after initial completion
- ⏸️ **On Hold** - Feature implementation is paused
- ❌ **Cancelled** - Feature was cancelled

## Priority Legend

- **Critical** - Must be implemented immediately, blocks other work
- **High** - Important feature, should be implemented soon
- **Medium** - Standard priority, implement in normal workflow
- **Low** - Nice to have, can be deferred

## Quick Stats

- **Total Features:** 11
- **Completed:** 3 (27%)
- **In Progress:** 4 (36%)
- **Planned:** 4 (36%)

## Next Up

Based on dependencies and priority, the recommended order of implementation:

1. **Feature 2000** - Unit Test Coverage (Critical, depends on 1000-1006)
2. **Feature 2001** - PHPStan Compliance (High, depends on 1000-1006)
3. **Feature 3000** - Complete API Documentation (High, in progress)
4. **Feature 4000** - CI/CD Pipeline (High, depends on 2000, 2001)

## Feature Dependencies

```
1000 ArrayUtil ────┐
1001 StringUtil ───┤
1002 Idx ──────────┤
1003 DateUtil ─────┼──> 2000 Unit Tests ──┐
1004 MoneyUtil ────┤                       ├──> 4000 CI/CD
1005 ObjectUtil ───┤                       │
1006 SearchUtil ───┴──> 2001 PHPStan ─────┘
                   │
                   └──> 3000 API Docs
```

## Roadmap

### Phase 1: Foundation (Current)
- Complete core utilities (1003, 1004, 1006)
- Document feature management process
- Set up feature tracking

### Phase 2: Quality (Next)
- Implement comprehensive testing (2000)
- Achieve PHPStan compliance (2001)
- Complete API documentation (3000)

### Phase 3: Infrastructure
- Set up CI/CD pipeline (4000)
- Automate quality checks
- Prepare for v1.0.0 release

### Phase 4: Release
- Final verification
- Tag v1.0.0
- Publish to Packagist

---

**Last Updated:** November 9, 2025  
**Next Review:** Weekly
