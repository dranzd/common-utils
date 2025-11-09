# Common Utils - General Development Plan

## Project Overview

**Project:** dranzd/common-utils  
**Description:** Framework-agnostic utility functions for PHP 8.3+ applications  
**Status:** Migration Phase - 70% Complete  
**Version:** 1.0.0 (Pre-release)

## Vision

Create a lightweight, framework-agnostic utility library that provides essential PHP utilities for array manipulation, string conversion, safe property access, date handling, money calculations, object manipulation, and search operations.

## Core Principles

1. **Framework Agnostic** - Zero framework dependencies, pure PHP only
2. **Type Safe** - Full PHP 8.3 type hints and strict types
3. **Lightweight** - Minimal dependencies (only ext-json)
4. **Well Tested** - Comprehensive test coverage (≥80%)
5. **Well Documented** - Clear API documentation and examples
6. **PSR-12 Compliant** - Follow PHP coding standards

## Current State (November 9, 2025)

### ✅ Completed Work

1. **Migration from Event Sourcing Library** (Steps 1-6)
   - Removed event sourcing components
   - Removed framework dependencies (Laravel, Laminas)
   - Updated namespaces to `Dranzd\Common\Utils`
   - Created framework-agnostic implementations
   - Updated package configuration
   - Created comprehensive documentation

2. **Core Utilities Implemented**
   - ArrayUtil - Array key case conversion
   - StringUtil - String case conversion
   - Idx - Safe property access
   - DateUtil - Date manipulation (basic)
   - MoneyUtil - Money calculations (basic)
   - ObjectUtil - Object manipulation
   - SearchUtil - Search and filtering (basic)

3. **Documentation**
   - README.md with examples and API reference
   - CONTRIBUTING.md with development guidelines
   - CHANGELOG.md with version history
   - API documentation for ArrayUtil, StringUtil, Idx
   - Migration documentation

4. **Infrastructure**
   - Docker setup with utility script
   - Composer configuration
   - PHPUnit configuration
   - PHPStan configuration
   - PHP_CodeSniffer configuration

### 🚧 In Progress

1. **Feature Management System**
   - Creating feature tracking system
   - Documenting feature management process
   - Creating initial feature specifications

### ⏳ Pending Work

1. **PHP 8.3 Enhancements** (Step 7)
   - Review for PHP 8.3 improvements
   - Add typed class constants
   - Use readonly properties where appropriate
   - Ensure PHPStan level 8+ compliance

2. **Comprehensive Testing** (Step 8)
   - Create test files for all utilities
   - Achieve ≥80% code coverage
   - Add edge case tests
   - Run full quality checks

3. **Complete API Documentation** (Step 6 - Partial)
   - DateUtil API documentation
   - MoneyUtil API documentation
   - ObjectUtil API documentation
   - SearchUtil API documentation

4. **CI/CD Setup** (Step 9)
   - GitHub Actions workflow
   - Automated testing
   - Coverage reporting
   - Quality checks

5. **Release** (Step 10)
   - Final verification
   - Tag v1.0.0
   - Publish to Packagist

## Feature Roadmap

### Series 1000: Core Utilities (Foundation)

These features represent the existing utilities that need enhancement and testing.

- **1000** - ArrayUtil Core Implementation
- **1001** - StringUtil Core Implementation
- **1002** - Idx Safe Access Implementation
- **1003** - DateUtil Core Implementation
- **1004** - MoneyUtil Core Implementation
- **1005** - ObjectUtil Core Implementation
- **1006** - SearchUtil Core Implementation

### Series 2000: Testing & Quality Assurance

Comprehensive testing and quality improvements.

- **2000** - Unit Test Coverage for All Utilities
- **2001** - Integration Test Suite
- **2002** - PHPStan Level 8+ Compliance
- **2003** - Code Style Enforcement (PSR-12)
- **2004** - Edge Case Testing
- **2005** - Performance Benchmarking

### Series 3000: Documentation & Examples

Complete documentation for all utilities.

- **3000** - Complete API Documentation
- **3001** - Usage Examples Library
- **3002** - Migration Guides
- **3003** - Best Practices Guide
- **3004** - Video Tutorials (Future)

### Series 4000: Development Tools & Infrastructure

CI/CD and development tooling.

- **4000** - GitHub Actions CI/CD Pipeline
- **4001** - Automated Release Process
- **4002** - Code Coverage Reporting
- **4003** - Docker Development Environment Enhancement
- **4004** - Development Utility Scripts

### Series 5000: Performance & Optimization

Performance improvements and optimizations.

- **5000** - Performance Profiling
- **5001** - ArrayUtil Optimization
- **5002** - StringUtil Optimization
- **5003** - Caching Strategies (if applicable)
- **5004** - Memory Usage Optimization

### Series 6000: Future Enhancements

Planned features for future versions.

- **6000** - Additional String Utilities (truncate, slug, sanitize)
- **6001** - Enhanced Date Utilities (timezone, relative dates)
- **6002** - Collection Utilities (map, filter, reduce)
- **6003** - Validation Utilities (email, URL, UUID)
- **6004** - File Path Utilities (normalize, join, resolve)
- **6005** - JSON Utilities (validate, transform)
- **6006** - XML Utilities (parse, transform)

## Implementation Phases

### Phase 1: Foundation (Current - Week 1)

**Goal:** Complete migration and establish feature management system

**Features:**
- Feature management system setup
- Document existing utilities as features (1000-1006)
- Complete remaining API documentation

**Deliverables:**
- Feature tracking system in place
- All existing utilities documented as features
- Complete API documentation

**Timeline:** 1 week

### Phase 2: Quality & Testing (Week 2)

**Goal:** Achieve comprehensive test coverage and quality standards

**Features:**
- 2000 - Unit Test Coverage
- 2001 - Integration Tests
- 2002 - PHPStan Level 8+
- 2003 - Code Style Enforcement
- 2004 - Edge Case Testing

**Deliverables:**
- ≥80% test coverage
- All tests passing
- PHPStan level 8+ passing
- PSR-12 compliant

**Timeline:** 1 week

### Phase 3: Infrastructure (Week 3)

**Goal:** Set up CI/CD and automated workflows

**Features:**
- 4000 - GitHub Actions CI/CD
- 4001 - Automated Releases
- 4002 - Coverage Reporting
- 4003 - Docker Enhancement

**Deliverables:**
- Working CI/CD pipeline
- Automated testing on push
- Coverage reports
- Enhanced development environment

**Timeline:** 1 week

### Phase 4: Release v1.0.0 (Week 4)

**Goal:** Publish first stable release

**Tasks:**
- Final verification
- Documentation review
- Test in fresh project
- Create git tag v1.0.0
- Publish to Packagist

**Deliverables:**
- Published v1.0.0 on Packagist
- Complete documentation
- Working CI/CD
- Ready for production use

**Timeline:** 3-5 days

### Phase 5: Enhancement (Post v1.0.0)

**Goal:** Add new features and optimizations

**Features:**
- Series 5000 - Performance optimizations
- Series 6000 - New utilities

**Deliverables:**
- Performance improvements
- New utility functions
- Enhanced capabilities

**Timeline:** Ongoing

## Success Metrics

### Code Quality

- ✅ All files use strict types
- ✅ All methods have type hints
- ✅ All methods have PHPDoc
- ✅ PSR-12 compliant
- ⏳ PHPStan level 8+ passing
- ⏳ ≥80% test coverage

### Documentation Quality

- ✅ README complete
- ⏳ API documentation complete (3 of 7 done)
- ✅ Contributing guidelines
- ✅ Changelog
- ⏳ Usage examples for all utilities

### Package Quality

- ✅ composer.json configured
- ✅ Namespace updated
- ✅ Autoloader working
- ✅ No framework dependencies
- ⏳ All tests passing
- ⏳ CI/CD pipeline working
- ⏳ Published to Packagist

## Risk Management

### High Priority Risks

1. **Test Coverage Gaps**
   - **Risk:** Insufficient test coverage could lead to bugs
   - **Mitigation:** Comprehensive test suite with ≥80% coverage
   - **Status:** In progress

2. **PHP 8.3 Compatibility Issues**
   - **Risk:** Code may not work correctly on PHP 8.3
   - **Mitigation:** Thorough testing on PHP 8.3, use CI/CD
   - **Status:** Pending verification

### Medium Priority Risks

1. **Documentation Completeness**
   - **Risk:** Incomplete documentation reduces adoption
   - **Mitigation:** Complete all API documentation before v1.0.0
   - **Status:** In progress

2. **Performance Issues**
   - **Risk:** Utilities may not be optimized
   - **Mitigation:** Performance profiling and optimization (Phase 5)
   - **Status:** Planned

### Low Priority Risks

1. **Breaking Changes in Future PHP Versions**
   - **Risk:** PHP 8.4+ may introduce breaking changes
   - **Mitigation:** Monitor PHP releases, update as needed
   - **Status:** Monitoring

## Dependencies

### Runtime Dependencies

- PHP ^8.3
- ext-json

### Development Dependencies

- phpunit/phpunit ^11.0
- phpstan/phpstan ^1.12
- squizlabs/php_codesniffer ^3.10

### External Dependencies

- None (framework-agnostic)

## Team & Resources

### Current Team

- Dranzd (Lead Developer)
- Cascade AI Assistant (Development Support)

### Required Skills

- PHP 8.3+ expertise
- Unit testing (PHPUnit)
- Static analysis (PHPStan)
- Documentation writing
- CI/CD (GitHub Actions)

## Communication

### Documentation

- Feature specifications in `docs/features/`
- Progress tracking in `docs/PROGRESS.md`
- General plan in `docs/GENERAL-PLAN.md` (this file)
- API documentation in `docs/api/`

### Version Control

- Git commit messages reference feature numbers
- Pull requests link to feature specifications
- Semantic versioning (SemVer)

### Release Notes

- CHANGELOG.md updated with each release
- GitHub releases with detailed notes
- Packagist automatic updates

## Next Actions (Immediate)

1. **Create Initial Feature Specifications** (Today)
   - Create features 1000-1006 for existing utilities
   - Document current state and requirements
   - Identify gaps and needed work

2. **Complete API Documentation** (This Week)
   - DateUtil.md
   - MoneyUtil.md
   - ObjectUtil.md
   - SearchUtil.md

3. **Begin Testing Phase** (Next Week)
   - Create test files for all utilities
   - Implement comprehensive test coverage
   - Run quality checks

4. **Set Up CI/CD** (Week 3)
   - GitHub Actions workflow
   - Automated testing
   - Coverage reporting

5. **Release v1.0.0** (Week 4)
   - Final verification
   - Tag and publish

## Long-Term Vision

### v1.x Series (2025-2026)

- Stable, well-tested core utilities
- Comprehensive documentation
- Strong community adoption
- Regular maintenance updates

### v2.x Series (2026+)

- PHP 8.4+ support
- Performance optimizations
- New utility categories
- Enhanced capabilities

### Ecosystem Growth

- dranzd/common-assert (assertions library)
- dranzd/common-valueobject (value objects)
- dranzd/common-messaging (messaging patterns)
- dranzd/common-event-sourcing (event sourcing)

## Conclusion

The common-utils library is well-positioned to become a foundational utility library for PHP 8.3+ applications. With a clear roadmap, comprehensive testing, and strong documentation, it will provide reliable, framework-agnostic utilities for the PHP community.

The feature management system will help track progress, maintain quality, and ensure all work is properly documented and completed.

---

**Version:** 1.0  
**Created:** November 9, 2025  
**Last Updated:** November 9, 2025  
**Next Review:** November 16, 2025
