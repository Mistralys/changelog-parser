# Constraints & Rules

## Established Coding Standards

### Type Systems

#### Strict Types Declaration
**Rule**: All PHP files must begin with `declare(strict_types=1);`

**Rationale**: Ensures type safety and prevents implicit type coercion bugs

**Example**:
```php
<?php

declare(strict_types=1);

namespace Mistralys\ChangelogParser;
```

#### Type Hints Required
**Rule**: All method parameters and return types must have explicit type hints

**Exceptions**: Mixed arrays where the structure is documented in PHPDoc

**Example**:
```php
// Good
public function getVersions() : array  // @var ChangelogVersion[] in PHPDoc

// Bad
public function getVersions()  // No return type
```

---

## Architecture Constraints

### Reader Pattern Rules

#### Abstract Base Enforcement
**Rule**: All format readers must extend `BaseReader`

**Requirements**:
- Must implement `getVersions() : array`
- Must extend `OperationResult` (via BaseReader)
- Must use `makeError()` for validation failures

#### Reader Responsibilities
**Rule**: Readers only parse and extract; no business logic

**Allowed**:
- Parse input format
- Create `ChangelogVersion` and `BaseChangeEntry` objects
- Validate input format
- Report errors

**Not Allowed**:
- Version comparison logic
- Change filtering or transformation
- API features (belongs in ChangelogParser)

### Change Entry Type System

#### Type Hierarchy Rule
**Rule**: All change entry types must extend `BaseChangeEntry`

**Requirements**:
- No additional public methods unless necessary
- Type distinction via inheritance, not flags
- No cross-cutting concerns (logging, persistence)

#### No Setters Rule
**Rule**: Change entries are immutable after construction

**Rationale**: Value objects should be immutable for safety

---

## Naming Conventions

### Class Naming
**Rule**: Use descriptive, unabbreviated names

**Patterns**:
- Readers: `*Reader` suffix (e.g., `MarkdownChangelogReader`)
- Changes: Descriptive noun (e.g., `ContentChange`, not `CChange`)
- Exceptions: `*Exception` suffix

### Method Naming

#### Query Methods
**Rule**: Use `get*()` for retrieving data, `is*()` for boolean checks

**Examples**:
- `getVersions()`, `getLatestVersion()`, `isValid()`, `isOptional()`

#### Factory Methods
**Rule**: Static creators use `create*()` or `parse*()` prefixes

**Examples**:
- `parseMarkdownFile()`, `createFromFile()`, `fromArray()`

#### Required vs Optional
**Rule**: Use `require*()` for methods that throw on failure, `get*()` for nullable returns

**Example**:
```php
public function getLatestVersion() : ?ChangelogVersion  // Returns null if none
public function requireLatestVersion() : ChangelogVersion  // Throws exception
```

### Constant Naming

#### Error Codes
**Rule**: `ERROR_*` prefix with descriptive name

**Format**: `public const ERROR_DESCRIPTIVE_NAME = 123xxx;`

**Example**:
```php
public const ERROR_NO_LAST_VERSION_AVAILABLE = 123101;
public const ERROR_UNKNOWN_VERSION_NUMBER = 123102;
```

#### Serialization Keys
**Rule**: `SERIALIZED_*` prefix for array keys

**Example**:
```php
public const SERIALIZED_NUMBER = 'number';
public const SERIALIZED_CHANGES = 'changes';
```

---

## Error Handling Rules

### Exception vs OperationResult

#### Use OperationResult For:
- **Input validation failures** (bad file format, missing file)
- **Expected error conditions** that users might handle gracefully
- **Reader-level errors** during parsing

**Pattern**:
```php
if ($condition) {
    $this->makeError('Message', self::ERROR_CODE);
    return;
}
```

#### Use Exceptions For:
- **Programming errors** (requesting unknown version)
- **Unexpected conditions** that indicate bugs
- **Unrecoverable errors** (JSON decode failure)

**Pattern**:
```php
throw new ChangelogParserException(
    'Short message',
    'Detailed explanation with context',
    self::ERROR_CODE
);
```

### Error Code Ranges

**Rule**: Each class has its own error code range

**Ranges**:
- `ChangelogParser`: 1231xx
- `BaseChangeEntry`: 1236xx
- `ChangelogVersion`: 1335xx
- `JSONChangelogReader`: 1237xx
- `MarkdownChangelogReader`: 1347xx

---

## Documentation Standards

### PHPDoc Requirements

#### Class Documentation
**Required**:
- `@package` and `@subpackage` tags
- `@author` tag
- Brief description of purpose

#### Method Documentation
**Required For**:
- Public methods with complex return types
- Methods that throw exceptions
- Array return types (specify element types)

**Format**:
```php
/**
 * Gets all versions from the changelog.
 *
 * @return ChangelogVersion[]
 * @throws ChangelogParserException
 */
public function getVersions() : array
```

#### Array Type Documentation
**Rule**: Use PHPDoc to specify array element types

**Examples**:
```php
/** @var ChangelogVersion[] */
private array $versions;

/** @return string[] */
public function getVersionNumbers() : array
```

---

## File I/O Constraints

### Current State: Synchronous Operations
**Rule**: All file operations are currently synchronous (blocking)

**Implementation**:
- Uses `FileInfo::factory($path)->getContents()`
- Uses `JSONFile::factory($path)->parse()`
- No async/await patterns
- No streams or generators

### File Wrapper Requirement
**Rule**: Never use raw PHP file functions; use application-utils wrappers

**Required**:
```php
// Good
$content = FileInfo::factory($path)->getContents();
$json = JSONFile::factory($path)->parse();

// Bad
$content = file_get_contents($path);  // Don't use
$json = json_decode(file_get_contents($path));  // Don't use
```

**Rationale**: Wrappers provide consistent error handling and type safety

---

## Testing Constraints

### Test Organization
**Rule**: Tests must be organized by feature/functionality, not by class

**Structure**:
- `ParsingTests` - Core parsing functionality
- `PersistenceTests` - Serialization features
- `MarkdownReaderTests` - Markdown-specific tests
- `StandardParsingTests` - Standard format compliance

### Test Base Class Usage
**Rule**: All test classes must extend `ChangelogParserTestCase`

**Benefits**:
- Shared fixture loading
- Helper methods (e.g., `createTestChangelogParser()`)
- Consistent setup/teardown

### Fixture Files
**Rule**: Test data files must be in `tests/files/`

**Naming**: Descriptive names indicating format and content
- `changelog-standard.md`
- `changelog-categorized.md`

---

## Versioning & Compatibility

### PHP Version Constraint
**Rule**: Minimum PHP 8.4 required

**Rationale**: Uses modern PHP features (typed properties, return types, etc.)

**Impact**: Cannot use on older PHP versions without significant refactoring

### Semantic Versioning
**Rule**: Package follows semantic versioning

**Implications**:
- **Major version bump**: Breaking API changes
- **Minor version bump**: New features, backward compatible
- **Patch version bump**: Bug fixes only

### Dependency Constraints
**Rule**: Use `>=` for dependencies, not `^` or `~`

**Current**:
```json
"mistralys/application-utils": ">=2.3.2",
"mistralys/version-parser": ">=2.0"
```

**Rationale**: Ensures minimum required features are available

---

## Performance Guidelines

### No Premature Optimization
**Rule**: Optimize only when profiling identifies bottlenecks

**Current Approach**: Simple, readable code over clever optimizations

### Memory Efficiency
**Guideline**: Suitable for changelogs up to ~1000 versions

**Trade-off**: Eager loading simplifies code at cost of memory

**Future Consideration**: If handling very large changelogs (> 1MB), consider streaming

---

## Security Considerations

### Input Validation
**Rule**: Always validate external input (file paths, version strings)

**Current**:
- File existence checked before reading
- Version strings validated via `VersionParser`
- JSON structure validated before deserialization

### No User-Supplied Code Execution
**Rule**: Never execute user-supplied strings as code

**Safe**: All parsing is declarative pattern matching, no `eval()` or similar

### Dependency Security
**Rule**: Use `roave/security-advisories` in dev dependencies

**Purpose**: Prevents installation of packages with known vulnerabilities

---

## Deprecation Policy

### Marking Deprecated Features
**Rule**: Use `@deprecated` PHPDoc tag with version and alternative

**Format**:
```php
/**
 * @deprecated Since 2.0, use getVersionNumbers() instead
 */
public function getNumbers() : array
```

### Removal Timeline
**Guideline**: Deprecated features removed in next major version

**Example**: Feature deprecated in 1.5.0 → removed in 2.0.0

---

## Code Style Enforcement

### Static Analysis
**Tool**: PHPStan (level configurable in `tests/config/phpstan.neon`)

**Run**: `tests/run-phpstan` or `tests/run-phpstan.bat`

**Rule**: Code must pass static analysis before commit

### Unit Testing
**Tool**: PHPUnit 9.5+

**Config**: `phpunit.xml` at project root

**Rule**: All public methods should have test coverage

**Run**: `vendor/bin/phpunit` or via PHPUnit integration

---

## Git & Repository Conventions

### Changelog Maintenance
**Rule**: Update `changelog.md` for all user-facing changes

**Format**: Follow the same format the parser expects (dogfooding)

### Commit Messages
**Guideline**: Use conventional commit format

**Examples**:
- `feat: Add support for pre-release versions`
- `fix: Correct parser crash on empty lines`
- `docs: Update API reference`

---

## Future-Proofing Considerations

### Extension Points
**Design**: Abstract bases allow new reader formats without breaking changes

**Example**: Could add `XMLChangelogReader` by extending `BaseReader`

### Serialization Format Stability
**Rule**: JSON format is part of public API; changes require major version bump

**Rationale**: Users may persist changelog data; format changes break existing files
