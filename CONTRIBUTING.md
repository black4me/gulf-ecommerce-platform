# Contributing to Gulf eCommerce Platform

Thank you for your interest in contributing! We welcome contributions from developers of all skill levels.

## Code of Conduct

This project adheres to a Code of Conduct. By participating, you are expected to uphold this code.

## How Can I Contribute?

### Reporting Bugs
- Check existing issues to avoid duplicates
- Include description, steps to reproduce, and expected behavior
- Provide screenshots or error messages
- Include your environment details

### Suggesting Enhancements
- Use clear descriptive titles
- Provide detailed use cases
- Explain why this enhancement would be useful
- List similar features in other projects

### Pull Requests
1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Make your changes following code standards
4. Write tests for new functionality
5. Commit with clear messages: `git commit -am 'Add feature'`
6. Push to your fork: `git push origin feature/my-feature`
7. Create a Pull Request with detailed description

## Development Setup

See [SETUP.md](SETUP.md) for development environment setup.

## Code Style

- PHP: Follow PSR-12
- JavaScript: Use ES6 standards
- Vue: Follow Vue 3 best practices
- CSS: Use BEM naming convention for SCSS

## Git Workflow

```bash
# Create branch from main
git checkout -b feature/feature-name

# Make changes
git add .

# Commit with clear messages
git commit -m 'feat: description of change'

# Push to origin
git push origin feature/feature-name
```

## Commit Message Convention

- `feat:` A new feature
- `fix:` A bug fix
- `docs:` Documentation only changes
- `style:` Changes that don't affect functionality
- `refactor:` Code changes without feature changes
- `test:` Adding or updating tests
- `chore:` Build process, dependencies, etc

## Testing

```bash
# Run all tests
docker-compose exec app php artisan test

# Run specific test
docker-compose exec app php artisan test --filter=TestName
```

## Documentation

Update README.md and relevant documentation when making changes.

## Licensing

By contributing, you agree that your contributions will be licensed under the MIT License.

## Questions?

Feel free to open an issue for any questions or discussions.
