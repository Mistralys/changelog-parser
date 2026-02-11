# Project Manifest - Changelog Parser

This directory contains the **Source of Truth** documentation for AI agent sessions working with the Changelog Parser codebase. These documents provide a comprehensive overview without requiring line-by-line code reading.

## Quick Navigation

### Core Documentation

- **[Tech Stack & Patterns](tech-stack.md)** - Runtime environment, dependencies, and architectural patterns
- **[Public API Reference](public-api.md)** - Method signatures and public interfaces for all classes
- **[File Tree](file-tree.md)** - Complete directory structure with descriptions
- **[Data Flows](data-flows.md)** - How components interact and process data
- **[Constraints & Rules](constraints.md)** - Coding standards and established conventions

## Project Overview

**Changelog Parser** is a PHP library that parses Markdown-formatted changelog files and provides a structured API for accessing version information and changes.

### Key Features

- Parse Markdown changelogs with flexible version formats
- Support for categorized changes (content, global, misc)
- Change type classification (mandatory, optional, neutral)
- JSON serialization/deserialization for persistence
- Sub-header support within versions
- Version comparison and lookup

### Primary Use Cases

1. Extract version information from changelog files
2. Analyze changes by category and type
3. Serialize changelog data to JSON for caching
4. Programmatically access latest version and changes

## Getting Started

For new AI agents:

1. Read [tech-stack.md](tech-stack.md) to understand the foundation
2. Review [public-api.md](public-api.md) for available interfaces
3. Check [data-flows.md](data-flows.md) to understand processing
4. Consult [constraints.md](constraints.md) before making changes

## Manifest Maintenance

Last Updated: February 11, 2026  
Manifest Version: 1.1  
Codebase Version: Reflects PHP 8.4 upgrade (typed constants, modern array functions)
