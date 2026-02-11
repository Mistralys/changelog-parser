# Tech Stack & Patterns

## Runtime Environment

### PHP Version
- **Minimum**: PHP 8.4
- **Type System**: Strict type declarations (`declare(strict_types=1)`) used throughout
- **Extensions**: JSON extension required

### Package Manager
- **Composer** - Dependency management and autoloading

## Dependencies

### Production Dependencies

#### mistralys/application-utils (>=2.3.2)
- Provides: `FileHelper`, `FileInfo`, `JSONFile`, `OperationResult`, `ClassHelper`
- Used for: File operations, JSON handling, result pattern

#### mistralys/version-parser (>=2.0)
- Provides: `VersionParser`
- Used for: Parsing and validating version strings
- Supports: Semantic versioning, snapshots, alpha/beta, custom formats

#### ext-json
- Native JSON encoding/decoding
- Used for: Serialization/deserialization

### Development Dependencies

- **PHPUnit** (>=9.5.26) - Unit testing framework
- **PHPStan** (>=1.9.2) - Static analysis tool
- **PHPStan PHPUnit Extension** (>=1.3.11) - PHPStan integration for tests
- **Roave Security Advisories** - Security vulnerability checker

## Architectural Patterns

### 1. Reader Pattern
- **Abstract Base**: `BaseReader` extends `OperationResult`
- **Implementations**: `MarkdownChangelogReader`, `JSONChangelogReader`
- **Purpose**: Isolate parsing logic by format type

### 2. Operation Result Pattern
- **Base Class**: `OperationResult` (from application-utils)
- **Usage**: `ChangelogParser` and `BaseReader` extend this
- **Benefits**: Consistent error handling, validation state tracking

### 3. Factory Methods
- Static creator methods like `parseMarkdownFile()`, `parseJSONFile()`
- Encapsulate object construction complexity
- Provide clear entry points for different input types

### 4. Type-Safe Hierarchies
- **Base Class**: `BaseChangeEntry` (abstract)
- **Concrete Types**: `ContentChange`, `GlobalChange`, `MiscChange`
- Change types are distinguished by inheritance, not flags

### 5. Value Objects
- `ChangelogVersion` - Immutable version representation
- `SubHeader` - Structured subheading data
- Both support array serialization/deserialization

### 6. Data Transfer Pattern
- All model classes implement `toArray()` and `fromArray()` methods
- Enables serialization for persistence and transmission
- Maintains type safety through reconstruction

## Code Organization

### Namespace Structure
```
Mistralys\ChangelogParser\
├── (root) - Core classes (Parser, Version, Exception)
├── Changes\ - Change entry type implementations
└── Reader\ - Format-specific reader implementations
```

### Autoloading
- **Strategy**: Classmap autoloading via Composer
- **Paths**: `src/` for production, `tests/TestClasses` for dev

## Testing Architecture

### Test Organization
- **Bootstrap**: `tests/bootstrap.php`
- **Test Suites**: In `tests/TestSuites/`
- **Test Base Class**: `ChangelogParserTestCase` - Provides helper methods
- **Fixtures**: Sample changelog files in `tests/files/`

### Static Analysis
- **Tool**: PHPStan with PHPUnit extension
- **Config**: `tests/config/phpstan.neon`
- **Scripts**: `run-phpstan` (bash), `run-phpstan.bat` (Windows)

## Design Principles

### Type Safety
- Strict type hints on all parameters and return types
- No mixed types except in array serialization
- Leverages PHP 8.4 type system features

### Immutability Preference
- Version objects are effectively immutable once constructed
- Changes are added during construction phase only
- No public setters on core models

### Single Responsibility
- Readers only parse and extract data
- Parser provides API and orchestration
- Changes represent specific entry types

### Separation of Concerns
- Parsing logic isolated in Reader classes
- Business logic in ChangelogParser
- Data models separate from processing

## Error Handling

### Exception Hierarchy
- **Base**: `ChangelogParserException` extends `BaseException` (from application-utils)
- **Error Codes**: Unique constants per error type (e.g., `ERROR_NO_LAST_VERSION_AVAILABLE`)

### Validation Strategy
- Readers report errors via `OperationResult` pattern
- Parser checks reader validity in constructor
- Exceptions thrown for programming errors (e.g., invalid version lookup)

## File I/O Patterns

### Reading Files
- **Abstraction**: Uses `FileInfo` from application-utils
- **Factory**: `FileInfo::factory()` creates instances from paths
- **Methods**: `exists()`, `getContents()` for reading

### JSON Operations
- **Abstraction**: Uses `JSONFile` from application-utils
- **Reading**: `JSONFile::factory($path)->parse()`
- **Writing**: `putData($array, $pretty)` method
- **Error Handling**: Throws `JsonException` on failure

### Current Pattern: Synchronous I/O
All file operations are currently synchronous (blocking).
