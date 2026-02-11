# Public API Reference

This document lists **signatures only** for all public properties, methods, and constructors. Implementation details are omitted.

---

## Core Classes

### ChangelogParser

**Extends**: `OperationResult`

**Purpose**: Main entry point for parsing changelog files.

#### Constants
```php
public const ERROR_NO_LAST_VERSION_AVAILABLE = 123101;
public const ERROR_UNKNOWN_VERSION_NUMBER = 123102;
```

#### Constructor
```php
public function __construct(BaseReader $reader)
```

#### Static Factory Methods
```php
public static function parseMarkdownFile($file) : ChangelogParser
public static function parseJSONFile($file) : ChangelogParser
```

#### Version Access
```php
public function getVersions() : array  // Returns ChangelogVersion[]
public function getLatestVersion() : ?ChangelogVersion
public function requireLatestVersion() : ChangelogVersion
public function versionExists(string $version) : bool
public function getVersionByNumber(string $number) : ChangelogVersion
public function getVersionNumbers() : array  // Returns string[]
```

#### Serialization
```php
public function toArray() : array
public function toJSON() : string
public function toJSONFile(string $filePath, bool $pretty=false) : JSONFile
```

---

### ChangelogVersion

**Purpose**: Represents a single version with its changes.

#### Constants
```php
public const ERROR_MISSING_SERIALIZED_KEYS = 133501;
public const ERROR_INVALID_SERIALIZED_KEY_TYPES = 133502;
public const SERIALIZED_NUMBER = 'number';
public const SERIALIZED_CHANGES = 'changes';
```

#### Constructor
```php
public function __construct(VersionParser $version, string $label='', int $level=1)
```

#### Properties Access
```php
public function getLabel() : string
public function getLevel() : int
public function getVersionInfo() : VersionParser
public function getNumber() : string
```

#### Changes Management
```php
public function getChanges() : array  // Returns BaseChangeEntry[]
public function addChange(BaseChangeEntry $change) : self
```

#### Freeform Content
```php
public function addTextLine(string $line) : self
public function addSubHeader(SubHeader $subHeader) : self
public function getFreeformText() : string
```

#### Serialization
```php
public function toArray() : array
public static function fromArray(array $data) : ChangelogVersion
```

---

### BaseChangeEntry

**Type**: Abstract class

**Purpose**: Base for all change entry types.

#### Constants
```php
public const ERROR_UNKNOWN_CHANGE_CLASS = 123601;

// Change types
public const CHANGE_NEUTRAL = 'neutral';
public const CHANGE_OPTIONAL = 'optional';
public const CHANGE_MANDATORY = 'mandatory';

// Serialization keys
public const SERIALIZED_ID = 'id';
public const SERIALIZED_TYPE = 'type';
public const SERIALIZED_CATEGORY = 'category';
public const SERIALIZED_TEXT = 'text';

// Parsing constants
public const LETTER_CONTENT = 'C';
public const LETTER_GLOBAL = 'G';
public const LETTER_EMPTY = ' ';
public const CHAR_OPTION = '{';
public const CHAR_MANDATORY = '(';
```

#### Constructor
```php
public function __construct(string $type, string $category, string $text)
```

#### Type Checks
```php
public function isOptional() : bool
public function isMandatory() : bool
public function isNeutral() : bool
```

#### Property Access
```php
public function getType() : string
public function getCategory() : string
public function getText() : string
```

#### Serialization
```php
public function toArray() : array
public static function fromArray(array $data) : BaseChangeEntry
public static function fromChangelog(string $char, string $letter, string $category, string $text) : BaseChangeEntry
```

---

### BaseReader

**Type**: Abstract class

**Extends**: `OperationResult`

**Purpose**: Base for format-specific readers.

#### Abstract Methods
```php
abstract public function getVersions() : array  // Returns ChangelogVersion[]
```

---

### ChangelogParserException

**Extends**: `BaseException` (from application-utils)

**Purpose**: Domain-specific exception type.

_(No additional public methods beyond BaseException)_

---

## Change Entry Types

All three concrete change classes extend `BaseChangeEntry` with no additional public API.

### ContentChange

**Extends**: `BaseChangeEntry`

_(No additional public methods)_

### GlobalChange

**Extends**: `BaseChangeEntry`

_(No additional public methods)_

### MiscChange

**Extends**: `BaseChangeEntry`

_(No additional public methods)_

---

## SubHeader

**Implements**: `Interface_Stringable`

**Purpose**: Represents a subheading within a version section.

#### Constructor
```php
public function __construct(int $level, string $label)
```

#### Property Access
```php
public function getLabel() : string
public function getLevel() : int
public function getBodyText() : string
```

#### Content Management
```php
public function addTextLine(string $line) : void
```

#### Rendering
```php
public function render() : string
public function __toString() : string
```

---

## Reader Implementations

### MarkdownChangelogReader

**Extends**: `BaseReader`

**Purpose**: Parses Markdown changelog files.

#### Constants
```php
public const ERROR_CHANGELOG_FILE_NOT_FOUND = 134701;
```

#### Constructor
```php
public function __construct(FileInfo $changelogFile)
```

#### Factory Method
```php
public static function create(FileInfo $changelogFile) : MarkdownChangelogReader
```

#### Implementation
```php
public function getVersions() : array  // Returns ChangelogVersion[]
```

---

### JSONChangelogReader

**Extends**: `BaseReader`

**Purpose**: Deserializes JSON changelog data.

#### Constants
```php
public const ERROR_INVALID_JSON_DATA = 123701;
```

#### Factory Methods
```php
public static function createFromFile(JSONFile $jsonFile) : JSONChangelogReader
public static function createFromString(string $json) : JSONChangelogReader
public static function createFromArray(array $data) : JSONChangelogReader
```

#### Implementation
```php
public function getVersions() : array  // Returns ChangelogVersion[]
```

---

## Type Signatures Summary

### Array Return Types (Detailed)

| Method | Returns |
|--------|---------|
| `ChangelogParser::getVersions()` | `ChangelogVersion[]` |
| `ChangelogParser::getVersionNumbers()` | `string[]` |
| `ChangelogParser::toArray()` | `array<int,array<string,mixed>>` |
| `ChangelogVersion::getChanges()` | `BaseChangeEntry[]` |
| `ChangelogVersion::toArray()` | `array{number:string,changes:array<int,array<string,mixed>>}` |
| `BaseChangeEntry::toArray()` | `array{id:string,type:string,category:string,text:string}` |
| `BaseReader::getVersions()` | `ChangelogVersion[]` |

### Exception-Throwing Methods

| Method | Can Throw |
|--------|-----------|
| `ChangelogParser::__construct()` | _(Via OperationResult)_ |
| `ChangelogParser::parseMarkdownFile()` | `FileHelper_Exception` |
| `ChangelogParser::requireLatestVersion()` | `ChangelogParserException` |
| `ChangelogParser::getVersionByNumber()` | `ChangelogParserException` |
| `ChangelogParser::toJSON()` | `JsonException` |
| `ChangelogParser::toJSONFile()` | `FileHelper_Exception` |
| `ChangelogParser::parseJSONFile()` | `FileHelper_Exception`, `JsonException` |
| `ChangelogVersion::fromArray()` | `ChangelogParserException` |
| `BaseChangeEntry::fromArray()` | `ChangelogParserException` |
| `BaseChangeEntry::fromChangelog()` | `ChangelogParserException` |
| `MarkdownChangelogReader::parseEntry()` | `ChangelogParserException` _(private method)_ |
| `JSONChangelogReader::createFromFile()` | `FileHelper_Exception`, `JsonException` |
| `JSONChangelogReader::createFromString()` | `JsonException`, `ChangelogParserException` |
| `JSONChangelogReader::createFromArray()` | `ChangelogParserException`, `JsonException` |

---

## Usage Patterns

### Typical Parse Flow
```php
// Parse Markdown
$parser = ChangelogParser::parseMarkdownFile('changelog.md');

// Access versions
$versions = $parser->getVersions();
$latest = $parser->getLatestVersion();

// Access changes
$changes = $latest->getChanges();
foreach ($changes as $change) {
    $change->getCategory();
    $change->getText();
    $change->isOptional();
}
```

### Persistence Flow
```php
// Save to JSON
$parser->toJSONFile('changelog.json', $pretty: true);

// Load from JSON
$parser = ChangelogParser::parseJSONFile('changelog.json');
```

### Version Lookup Flow
```php
if ($parser->versionExists('1.2.3')) {
    $version = $parser->getVersionByNumber('1.2.3');
}
```
