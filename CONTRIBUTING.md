# Contributing

Bug reports, fixes and well-scoped additions are welcome.

## Scope

This library is deliberately small. A new utility is a good fit when it:

- solves a problem native PHP does not already solve in one call,
- needs no runtime dependency beyond the PHP standard extensions already required,
- is stateless: no I/O and no global state.

If you are unsure, open an issue describing the use case before writing code.

## Setup

Requires PHP 8.3+ with `ext-mbstring`, and Composer.

```bash
git clone https://github.com/<your-username>/common-utils.git
cd common-utils
composer install
```

Or, with Docker instead of a local PHP:

```bash
make install
make check
```

## Quality gates

CI runs the same commands, and all of them must pass:

| Command | What it checks |
|---|---|
| `composer test` | PHPUnit suite |
| `composer phpstan` | PHPStan at max level with strict rules, no ignored errors |
| `composer cs-check` | PSR-12 with a 120 character line limit (`composer cs-fix` repairs most issues) |
| `composer check` | all of the above |

CI also enforces at least 95% line coverage (`composer test:coverage` needs pcov or Xdebug).

## Code conventions

- `declare(strict_types=1);`, `final` classes, static methods, full parameter and return types.
- Invalid input throws `\InvalidArgumentException` with a message that names the problem.
- Docblocks carry what the type system cannot express (array shapes, generics, `@throws`),
  not a restatement of the signature.
- Every behaviour change comes with a test. Prefer data providers with named cases, and cover
  edge cases: empty input, Unicode, boundaries, and each exception.

## Pull requests

1. Branch from `master`.
2. Keep the change focused; unrelated refactoring belongs in a separate pull request.
3. Add an entry under "Unreleased" in `CHANGELOG.md` for anything users can observe.
4. Update `README.md` and the relevant page in `docs/api` when the public API changes.
5. Write commit messages in the imperative mood, explaining why when it is not obvious.

The project follows [Semantic Versioning](https://semver.org/). Changing documented behaviour,
signatures or thrown exception types is a breaking change and needs a major release.

## Releasing

Maintainers release from `master`: add a dated section for the version to `CHANGELOG.md`, merge it,
then run the "Release" workflow with that version. It checks that CI passed on the commit, creates
the `v<version>` tag and publishes a GitHub release with that changelog section as its notes.

## Conduct

Be respectful and constructive: discuss the code, not the person, and assume good intent.

## Security

Please report vulnerabilities privately; see [SECURITY.md](SECURITY.md).
