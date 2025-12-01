# Contributing

Contributions are **welcome** and will be fully **credited**.

We accept contributions via Pull Requests on [Github](https://github.com/williamug/versioning).

## Pull Requests

- **Add tests!** - Your patch won't be accepted if it doesn't have tests.

- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow [SemVer v2.0.0](http://semver.org/). Randomly breaking public APIs is not an option.

- **Create feature branches** - Don't ask us to pull from your main branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

## Running Tests

```bash
composer test
```

## Running Static Analysis

```bash
composer analyse
```

## Code Style

We use [Laravel Pint](https://github.com/laravel/pint) for code styling.

```bash
composer format
```

To check code style without fixing:

```bash
vendor/bin/pint --test
```

## Development Setup

1. Fork the repository
2. Clone your fork: `git clone https://github.com/your-username/versioning.git`
3. Install dependencies: `composer install`
4. Create a branch: `git checkout -b my-new-feature`
5. Make your changes
6. Run tests: `composer test`
7. Run static analysis: `composer analyse`
8. Format code: `composer format`
9. Commit your changes: `git commit -am 'Add some feature'`
10. Push to the branch: `git push origin my-new-feature`
11. Submit a pull request

## Guidelines

### Coding Standards

- Follow PSR-12 coding standards
- Use type hints wherever possible
- Add docblocks for classes and methods
- Keep methods small and focused

### Testing

- Write tests for all new features
- Ensure tests are clear and descriptive
- Use Pest syntax for consistency
- Aim for high test coverage

### Documentation

- Update README.md if you change functionality
- Add PHPDoc blocks to new methods
- Include usage examples for new features

### Commit Messages

- Use clear and meaningful commit messages
- Start with a verb in present tense (Add, Update, Fix, Remove)
- Reference issue numbers when applicable

Example:
```
Add cache clearing functionality

- Implement clearCache() method
- Add tests for cache clearing
- Update documentation

Fixes #123
```

## Questions?

If you have any questions about contributing, feel free to:
- Open an issue
- Start a discussion
- Contact the maintainer

**Happy coding!**
