# Contributing to Laravel n8n Package

Thank you for considering contributing to the Laravel n8n package! This document provides guidelines and information for contributors.

## Code of Conduct

This project adheres to the [Contributor Covenant Code of Conduct](https://www.contributor-covenant.org/version/2/1/code_of_conduct/). By participating, you are expected to uphold this code.

## How to Contribute

### Reporting Bugs

1. **Check existing issues** - Search through existing issues to avoid duplicates
2. **Use the issue template** - Provide as much relevant information as possible
3. **Include reproduction steps** - Clear steps to reproduce the issue
4. **Environment details** - Laravel version, PHP version, n8n version

### Suggesting Features

1. **Check the roadmap** - Review existing feature requests and roadmap
2. **Open a discussion** - Use GitHub Discussions for feature proposals
3. **Provide use cases** - Explain why the feature would be beneficial
4. **Consider implementation** - Think about how it could be implemented

### Pull Requests

1. **Fork the repository**
2. **Create a feature branch** (`git checkout -b feature/amazing-feature`)
3. **Follow coding standards** (see below)
4. **Write tests** for your changes
5. **Update documentation** if necessary
6. **Commit your changes** (`git commit -m 'Add amazing feature'`)
7. **Push to the branch** (`git push origin feature/amazing-feature`)
8. **Open a Pull Request**

## Development Setup

### Prerequisites

- PHP 8.1+
- Composer
- Laravel development environment
- n8n instance for testing

### Setup Steps

```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/n8n-laravel.git
cd n8n-laravel

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Configure your n8n instance
# Edit .env with your n8n details

# Run tests
vendor/bin/pest
```

## Coding Standards

### PSR Standards

- Follow PSR-1, PSR-2, and PSR-12
- Use PSR-4 autoloading

### Laravel Guidelines

Follow the Laravel coding guidelines outlined in `.github/copilot-instructions.md`:

- Use camelCase for non-public-facing strings
- Use short nullable notation: `?string` not `string|null`
- Always specify `void` return types when methods return nothing
- Use typed properties, not docblocks
- Use constructor property promotion when all properties can be promoted
- Follow the "happy path last" principle
- Avoid `else` statements when possible

### PHP Standards

```php
<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Example;

class ExampleClass
{
    public function __construct(
        private readonly string $property
    ) {
    }

    public function exampleMethod(?string $input = null): string
    {
        if (empty($input)) {
            return 'default';
        }

        return $input;
    }
}
```

### Documentation Standards

- Use PHPDoc blocks for complex methods
- Always import classnames in docblocks
- Use array shape notation for fixed keys
- Document return types for iterables with generics

## Testing Guidelines

### Writing Tests

- Use Pest framework
- Write descriptive test names
- Follow arrange-act-assert pattern
- Test both happy path and error conditions

```php
it('can create workflow with valid data', function () {
    // Arrange
    Http::fake([
        'localhost:5678/api/v1/workflows' => Http::response([
            'id' => 'wf-123',
            'name' => 'Test Workflow'
        ])
    ]);

    $workflowData = ['name' => 'Test Workflow'];

    // Act
    $result = N8N::workflows()->create($workflowData);

    // Assert
    expect($result)->toHaveKey('id', 'wf-123');
});
```

### Test Coverage

- Aim for 80%+ test coverage
- Test all public methods
- Include edge cases and error conditions
- Mock external dependencies

### Running Tests

```bash
# Run all tests
vendor/bin/pest

# Run with coverage
vendor/bin/pest --coverage

# Run specific test
vendor/bin/pest tests/Feature/FacadeTest.php

# Run with filter
vendor/bin/pest --filter="can create workflow"
```

## Architecture Guidelines

### Design Patterns

When contributing, maintain the established design patterns:

1. **Facade Pattern** - Keep the facade interface simple and intuitive
2. **Adapter Pattern** - Maintain clean abstraction boundaries
3. **Strategy Pattern** - Ensure new strategies implement the interface
4. **Builder Pattern** - Keep builders fluent and chainable
5. **Observer Pattern** - Maintain event consistency

### Adding New Features

#### New API Endpoints

1. Add method to appropriate service interface
2. Implement in concrete service class
3. Add tests for the new functionality
4. Update documentation

#### New Services

1. Create service interface in `Contracts/`
2. Implement service class extending `BaseService`
3. Add service method to `AdapterInterface`
4. Register in `N8NAdapter`
5. Add comprehensive tests

#### New Strategies

1. Implement `StrategyInterface`
2. Add to strategy factory in service provider
3. Document configuration options
4. Add tests for strategy behavior

#### New Builders

1. Implement `BuilderInterface`
2. Follow fluent API patterns
3. Add reset functionality
4. Test all builder methods

#### New Observers

1. Implement `ObserverInterface`
2. Handle all lifecycle events appropriately
3. Test observer behavior
4. Document usage examples

## Documentation

### Code Documentation

- Document all public methods
- Use clear, descriptive names
- Include usage examples for complex features
- Keep comments up to date

### User Documentation

- Update README.md for new features
- Add examples to DOCUMENTATION.md
- Update configuration options
- Include migration guides for breaking changes

## Version Control

### Commit Messages

Use conventional commit format:

```
feat: add workflow transfer functionality
fix: resolve authentication timeout issue
docs: update builder pattern examples
test: add coverage for error scenarios
refactor: simplify adapter interface
```

### Branch Naming

- `feature/description` - New features
- `fix/description` - Bug fixes
- `docs/description` - Documentation updates
- `refactor/description` - Code refactoring

## Release Process

### Semantic Versioning

- **MAJOR** - Breaking changes
- **MINOR** - New features (backward compatible)
- **PATCH** - Bug fixes (backward compatible)

### Changelog

- Update CHANGELOG.md for all changes
- Follow Keep a Changelog format
- Include migration instructions for breaking changes

## Getting Help

### Communication Channels

- **GitHub Issues** - Bug reports and feature requests
- **GitHub Discussions** - General questions and ideas
- **Pull Request Reviews** - Code-specific discussions

### Code Review Process

1. Automated checks must pass
2. At least one maintainer review required
3. All conversations must be resolved
4. Tests must pass
5. Documentation must be updated

### Maintainer Response Times

- Initial response: Within 48 hours
- Code review: Within 1 week
- Release cycle: Monthly for minor versions

## Recognition

Contributors will be:
- Listed in the README.md
- Mentioned in release notes
- Credited in the package documentation

Thank you for contributing to make this package better for the Laravel and n8n communities!

---

For questions about contributing, please open a GitHub Discussion or reach out to the maintainers.
