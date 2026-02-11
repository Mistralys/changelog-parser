# Data Flows

## Overview

The Changelog Parser follows a linear, pipeline-style data flow:

```
Input Source → Reader → Parser → User Code
```

There is no UI layer - this is a library consumed by other PHP applications.

---

## Primary Data Flows

### Flow 1: Markdown File to Parsed Structure

**Entry Point**: `ChangelogParser::parseMarkdownFile($file)`

```
User Code
    ↓ calls parseMarkdownFile()
ChangelogParser
    ↓ creates FileInfo
FileInfo::factory($file)
    ↓ passes to reader
MarkdownChangelogReader::create($fileInfo)
    ↓ reads file contents
$fileInfo->getContents()
    ↓ splits into lines
explode("\n", $contents)
    ↓ iterates lines
foreach ($lines as $line)
    ↓ parses each line
parseLine($line)
    ↓ identifies headers/entries
parseHeader() / parseEntry()
    ↓ creates objects
new ChangelogVersion()
new BaseChangeEntry()
    ↓ collects versions
$this->versions[]
    ↓ returns reader
MarkdownChangelogReader with versions
    ↓ constructor validates
ChangelogParser($reader)
    ↓ extracts versions
$this->versions = $reader->getVersions()
    ↓ returns to user
ChangelogParser instance
```

**Key Decision Points**:
1. **Line type detection**: Header vs. entry vs. freeform text
2. **Version validation**: Uses `VersionParser` to validate version strings
3. **Entry categorization**: Parses bracket notation to determine change type
4. **Level tracking**: Determines when to stop capturing (higher-level headers)

---

### Flow 2: JSON Deserialization

**Entry Point**: `ChangelogParser::parseJSONFile($file)`

```
User Code
    ↓ calls parseJSONFile()
ChangelogParser
    ↓ creates JSONFile wrapper
JSONFile::factory($file)
    ↓ passes to reader
JSONChangelogReader::createFromFile($jsonFile)
    ↓ parses JSON
$jsonFile->parse()
    ↓ iterates array
foreach ($data as $versionData)
    ↓ reconstructs version
ChangelogVersion::fromArray($versionData)
    ↓ reconstructs changes
BaseChangeEntry::fromArray($changeData)
    ↓ collects versions
$this->versions[]
    ↓ returns reader
JSONChangelogReader with versions
    ↓ constructor validates
ChangelogParser($reader)
    ↓ returns to user
ChangelogParser instance
```

**Key Operations**:
1. **JSON parsing**: Validates structure and types
2. **Object reconstruction**: Factory methods recreate objects from arrays
3. **Type restoration**: Class names stored in serialized data determine concrete types

---

### Flow 3: Serialization to JSON

**Entry Point**: `ChangelogParser::toJSONFile($path)`

```
User Code
    ↓ calls toJSONFile()
ChangelogParser
    ↓ converts to array
$this->toArray()
    ↓ iterates versions
foreach ($versions as $version)
    ↓ version to array
$version->toArray()
    ↓ iterates changes
foreach ($changes as $change)
    ↓ change to array
$change->toArray()
    ↓ builds structure
array of arrays
    ↓ creates JSONFile
JSONFile::factory($filePath)
    ↓ writes data
$jsonFile->putData($array, $pretty)
    ↓ returns
JSONFile instance
```

**Serialization Format**:
```json
[
  {
    "number": "1.2.3",
    "changes": [
      {
        "id": "ContentChange",
        "type": "optional",
        "category": "Feature",
        "text": "Added new functionality"
      }
    ]
  }
]
```

---

## Parsing Logic Details

### Markdown Line Processing

The `MarkdownChangelogReader` uses a state machine approach:

**States**:
1. **No active version**: Looking for first version header
2. **Active version**: Capturing entries and text for current version
3. **Active subheader**: Capturing text under a subheading

**State Transitions**:

```
Initial State: No Active Version
    ↓
Version Header Found → Active Version
    ↓
├─ Change Entry (- ...) → Add to version
├─ Subheader → Active Subheader
│   ↓
│   Text Line → Add to subheader
│   ↓
│   Another Header → Return to Active Version
├─ Freeform Text → Add to version
└─ Higher/Equal Level Header → No Active Version
```

### Version Header Detection

**Regex Pattern**: `/v?([0-9.]+[0-9A-Z._\-]*) (.*)|v?([0-9.]+[0-9A-Z._\-]*)/six`

**Recognized Formats**:
- `# v1.2.3`
- `## 1.2.3`
- `### v1.2.3-ALPHA`
- `# 5.0 - Release Name`
- `# 5.0 | Release Name`
- `# 5.0 ~ Release Name`

**Validation**: Version string passed to `VersionParser::create()` which validates format.

### Change Entry Parsing

**Format 1 - With Brackets**:
```markdown
- {G} Category: Change text
- (C) Category: Change text
```

**Regex**: `/\A- `*([{(])([CG ])([})])`*([^:]*):(.*)\z/i`

**Components**:
1. Bracket char (`{` or `(`): Determines optional vs mandatory
2. Letter (`C`, `G`, or space): Determines entry type
3. Category: Text before colon
4. Text: Everything after colon

**Format 2 - Without Brackets**:
```markdown
- Category: Change text
- Uncategorized text
```

**Processing**:
- Split on `:` if present
- No category if no colon found

---

## Component Interactions

### Reader ↔ Parser Interaction

```
ChangelogParser
    ├─ Receives: BaseReader instance
    ├─ Checks: $reader->isValid()
    ├─ Extracts: $reader->getVersions()
    └─ Delegates: Error state from reader
```

**OperationResult Pattern**:
- Reader reports success/failure via inherited `OperationResult` methods
- Parser checks validity and propagates errors
- User code can call `$parser->isValid()` and `$parser->getErrorMessage()`

### Version ↔ Change Interaction

```
ChangelogVersion
    ├─ Owns: Array of BaseChangeEntry instances
    ├─ Provides: Read-only access via getChanges()
    ├─ Builder Pattern: addChange() for construction
    └─ No Removal: Once added, changes cannot be removed
```

### Change Type Resolution

```
BaseChangeEntry::fromChangelog($char, $letter, $category, $text)
    ↓
resolveID($letter)
    ├─ 'C' → ContentChange
    ├─ 'G' → GlobalChange
    └─ ' ' → MiscChange
    ↓
resolveType($char, $letter)
    ├─ '{' → CHANGE_OPTIONAL
    ├─ '(' + letter → CHANGE_MANDATORY
    ├─ '(' + no letter → CHANGE_NEUTRAL
    └─ else → CHANGE_NEUTRAL
    ↓
fromArray() creates concrete instance
```

---

## External Dependencies Flow

### File Operations

```
User provides path string
    ↓
FileInfo::factory($path)
    ↓ (from application-utils)
FileInfo object
    ├─ $fileInfo->exists() → bool
    └─ $fileInfo->getContents() → string
```

### Version Parsing

```
Version string (e.g., "1.2.3-ALPHA")
    ↓
VersionParser::create($string)
    ↓ (from version-parser package)
VersionParser object
    ├─ $vp->getVersion() → "1.2.3"
    ├─ $vp->getTagVersion() → "1.2.3"
    ├─ $vp->getBuildNumberInt() → int
    └─ (many other version accessors)
```

---

## Error Flow

### Validation Errors

```
Invalid Input
    ↓
Reader detects problem
    ↓
$this->makeError($message, $code)
    ↓ (OperationResult method)
Sets internal error state
    ↓
ChangelogParser constructor
    ↓
Checks $reader->isValid()
    ↓
Propagates error state
    ↓
User Code
    ↓
Can check $parser->isValid()
Can get $parser->getErrorMessage()
```

### Programming Errors

```
Invalid API usage (e.g., unknown version)
    ↓
Throws ChangelogParserException
    ↓
User Code should catch
```

---

## Performance Considerations

### Single-Pass Parsing
- Markdown reader processes file once, line by line
- No backtracking or multiple passes
- Results cached in memory

### Eager Loading
- All versions and changes loaded immediately
- No lazy loading or pagination
- Suitable for typical changelog sizes (< 1000 versions)

### Memory Footprint
- Entire file contents loaded into memory
- All parsed objects retained
- No streaming or incremental parsing

### Optimization Opportunities
- For very large changelogs (> 1MB), could add streaming support
- JSON caching reduces re-parsing markdown files
- Version lookup uses linear search (could be optimized with index)
