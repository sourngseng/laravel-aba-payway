# Contributing to Laravel ABA PayWay

Thank you for considering contributing to Laravel ABA PayWay! This document outlines the process for contributing to this project.

## Getting Started

1. Fork the repository on GitHub
2. Clone your fork locally
3. Create a new branch for your feature or bug fix
4. Make your changes
5. Run the tests to ensure everything is working
6. Commit your changes with a descriptive commit message
7. Push your branch to your fork
8. Create a Pull Request

## Development Setup

### Prerequisites

- PHP 8.1 or higher
- Composer
- Laravel (for testing)

### Installation

```bash
# Clone your fork
git clone https://github.com/yourusername/laravel-aba-payway.git
cd laravel-aba-payway

# Install dependencies
composer install

# Copy the example environment file if needed
cp .env.example .env
```

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
vendor/bin/phpunit --coverage-html coverage
```

## Coding Standards

This project follows PSR-12 coding standards. Please ensure your code adheres to these standards.

### Code Style

- Use meaningful variable and method names
- Add docblocks to all public methods
- Keep methods focused and concise
- Follow Laravel conventions where applicable

### Testing

- Write tests for new features
- Ensure existing tests pass
- Aim for good test coverage
- Use descriptive test method names

## Pull Request Process

1. **Create a feature branch** from `main`
2. **Write tests** for your changes
3. **Ensure all tests pass**
4. **Update documentation** if necessary
5. **Create a Pull Request** with a clear description

### Pull Request Guidelines

- Provide a clear description of the changes
- Reference any related issues
- Include screenshots for UI changes
- Ensure your branch is up to date with the main branch

## Reporting Issues

When reporting issues, please include:

- Laravel version
- PHP version
- Package version
- Steps to reproduce the issue
- Expected behavior
- Actual behavior
- Any error messages or logs

## Feature Requests

We welcome feature requests! Please:

- Check if the feature already exists
- Provide a clear description of the feature
- Explain the use case and benefits
- Consider if it fits the package's scope

## Code of Conduct

Please be respectful and constructive in all interactions. We want to maintain a welcoming environment for all contributors.

## Questions?

If you have questions about contributing, feel free to:

- Open an issue for discussion
- Ask questions in your Pull Request
- Reach out to the maintainers

Thank you for contributing to Laravel ABA PayWay!