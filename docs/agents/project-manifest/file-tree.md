# File Tree

## Project Root Structure

```
changelog-parser/
├── docs/                           # Documentation (includes this manifest)
│   └── agents/
│       └── project-manifest/       # AI agent reference documentation
├── src/                            # Source code
│   ├── Changes/                    # Change entry type classes
│   └── Reader/                     # Format-specific parser implementations
├── tests/                          # Test suite
│   ├── TestClasses/               # Test infrastructure classes
│   ├── TestSuites/                # Actual test cases
│   ├── config/                    # Test configuration (PHPStan, etc.)
│   ├── files/                     # Test fixtures (sample changelogs)
│   └── phpstan/                   # PHPStan-related files
├── changelog.md                   # Project's own changelog
├── composer.json                  # Composer dependencies and metadata
├── LICENSE                        # MIT license
├── phpunit.xml                    # PHPUnit configuration
└── README.md                      # Project documentation
```

## Source Code (`src/`)

### Root Level Classes

| File | Purpose | Type |
|------|---------|------|
| **ChangelogParser.php** | Main entry point and API facade | Concrete class |
| **BaseChangeEntry.php** | Abstract base for change entries | Abstract class |
| **BaseReader.php** | Abstract base for format readers | Abstract class |
| **ChangelogVersion.php** | Represents a single version with changes | Value object |
| **ChangelogParserException.php** | Domain-specific exception | Exception class |

### Changes Directory (`src/Changes/`)

Change entry implementations following type-safe hierarchy pattern:

| File | Purpose | Extends |
|------|---------|---------|
| **ContentChange.php** | Content-related changes (marked with 'C') | BaseChangeEntry |
| **GlobalChange.php** | Global/system-wide changes (marked with 'G') | BaseChangeEntry |
| **MiscChange.php** | Miscellaneous/uncategorized changes | BaseChangeEntry |
| **SubHeader.php** | Subheadings within version sections | Interface_Stringable |

### Reader Directory (`src/Reader/`)

Format-specific parsers:

| File | Purpose | Extends |
|------|---------|---------|
| **MarkdownChangelogReader.php** | Parses Markdown changelog files | BaseReader |
| **JSONChangelogReader.php** | Deserializes JSON changelog data | BaseReader |

## Test Suite (`tests/`)

### Test Infrastructure

| File/Directory | Purpose |
|----------------|---------|
| **bootstrap.php** | PHPUnit bootstrap file |
| **run-phpstan** | Bash script for static analysis |
| **run-phpstan.bat** | Windows batch script for static analysis |

### TestClasses Directory

| File | Purpose |
|------|---------|
| **ChangelogParserTestCase.php** | Base class for tests with helper methods |

### TestSuites Directory

| File | Tests |
|------|-------|
| **ParsingTests.php** | Version parsing, change extraction |
| **MarkdownReaderTests.php** | Markdown-specific parsing logic |
| **PersistenceTests.php** | JSON serialization/deserialization |
| **StandardParsingTests.php** | Standard changelog format parsing |

### Test Fixtures (`tests/files/`)

| File | Contains |
|------|----------|
| **changelog-standard.md** | Standard format with various version styles |
| **changelog-categorized.md** | Changelog with categorized changes |

### Configuration (`tests/config/`)

| File | Purpose |
|------|---------|
| **phpstan.neon** | PHPStan static analysis configuration |

## Key File Relationships

### Primary Data Flow
```
User Code
    ↓
ChangelogParser (entry point)
    ↓
BaseReader (abstract)
    ↓
├── MarkdownChangelogReader (parses MD)
└── JSONChangelogReader (deserializes JSON)
    ↓
ChangelogVersion (collection)
    ↓
BaseChangeEntry (abstract)
    ↓
├── ContentChange
├── GlobalChange
└── MiscChange
```

### Dependency Relationships
- **ChangelogParser** depends on: BaseReader, ChangelogVersion
- **BaseReader** depends on: ChangelogVersion, OperationResult
- **MarkdownChangelogReader** depends on: FileInfo, VersionParser, BaseChangeEntry
- **JSONChangelogReader** depends on: JSONFile, ChangelogVersion
- **ChangelogVersion** depends on: VersionParser, BaseChangeEntry, SubHeader
- **BaseChangeEntry** depends on: ClassHelper (for type identification)

## File Size Context

Most source files are compact:
- Core classes: 100-300 lines
- Change types: 10-20 lines (minimal implementations)
- Readers: 200-300 lines
- Test files: 50-150 lines each

This reflects focused, single-responsibility design.
