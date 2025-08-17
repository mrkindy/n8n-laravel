# Changelog

All notable changes to the Laravel n8n package will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2025-08-18

### Added
- Initial release of Laravel n8n package
- Facade pattern implementation for simple API access
- Adapter pattern for clean n8n API abstraction
- Strategy pattern supporting sync, async, and queued execution
- Builder pattern for constructing complex request payloads
- Observer pattern for request lifecycle monitoring
- Complete coverage of n8n Public API v1.1.1
- Event system integration with Laravel events
- Comprehensive error handling with custom exceptions
- Built-in logging and metrics observers
- OpenAPI schema parsing utilities
- Full Pest testing framework support
- Detailed documentation and usage examples

### Features
- **Workflows**: List, get, create, update, delete, activate, deactivate, transfer, tag management
- **Credentials**: Create, delete, schema retrieval, transfer
- **Executions**: List, get, delete with filtering support
- **Users**: Full user management (Enterprise features)
- **Tags**: Complete tag lifecycle management
- **Variables**: Variable management and operations
- **Projects**: Project management (Enterprise features)
- **Audit**: Security audit generation
- **Source Control**: Pull operations support

### Supported Laravel Versions
- Laravel 10.x
- Laravel 11.x

### Requirements
- PHP 8.1+
- n8n instance with API access
