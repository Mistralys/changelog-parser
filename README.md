# Changelog Parser

PHP library to parse Markdown-formatted change log files.

## Requirements

- PHP >= 7.4
- JSON extension
- [Composer](https://getcomposer.org)

## Installation

Add the package to your `composer.json` file with the following command:

```shell
composer require mistralys/changelog-parser
```

Also see the [Packagist page](https://packagist.org/packages/mistralys/changelog-parser).

## Supported changelog formats

The parser expects all versions to be listed as headers with the same
header level, and individual changes to be added as a list. Both of 
these examples will work:

```markdown
# v1.2.3
- Made some changes

# v1.2.2
- Lotsa code changes
- Added some documentation
```

```markdown
### v1.2.3
- Made some changes

### v1.2.2
- Lotsa code changes
- Added some documentation
```

### Version heading formats

All the following headings are valid formats that the parser will recognize:

```markdown
# v1

# v1.2

# v1.2.3

# 1

# 1.2

# 1.2.3

# 1.2.3-ALPHA

# 5.0 - Optional version label

# 5.0 | Optional version label

# 5.0 ~ Optional version label
```

### Nesting the changelog in a document

The way the parser analyzes the Markdown document means that the
changelog can be nested anywhere. The heading level will be inferred
from the first version heading it encounters.

In this example, the changelog is not a separate document, but is
nested in a subsection. 

```markdown
# Application name

## Usage

Learn how to use the application with this documentation.

## Change log

### v1.2.3
- Made some changes

### v1.2.2
- Lotsa code changes
- Added some documentation

## Credits

Many people contributed to the application.
```

> The changelog parser will stick to the first changelog it finds in
> the document, meaning that only the first of multiple, separate 
> version lists will be used, even if they have the same heading level. 

### Subheaders within versions

The parser will recognize subheaders within a version entry, and add
collect these as plain text to be accessed again later. This makes it
possible to further document things like breaking changes for example.

```markdown
# v2.0.0 - Complete rework (breaking)
- Code entirely refurbished
- Documentation rewritten

## Breaking changes
- Renamed all methods
- Renamed all files
```

Only the items below the version will be considered changes in the
version. The "Breaking changes" subheader and any additional content
is captured as text, which can be accessed via `getFreeformText()`:

```php
use Mistralys\ChangelogParser\ChangelogParser;

$version = ChangelogParser::parseMarkdownFile('changelog.md')->getVersionByNumber('2.0.0');

echo $version->getFreeformText();
```

This will output:

```markdown
## Breaking changes
- Renamed all methods
- Renamed all files
```

## Usage examples

### Fetch all versions

```php
use Mistralys\ChangelogParser\ChangelogParser;

$versions = ChangelogParser::parseMarkdownFile('changelog.md')->getVersions();

foreach($versions as $version)
{
    echo $version->getNumber().PHP_EOL;
}
```

### Fetch the latest version

```php
use Mistralys\ChangelogParser\ChangelogParser;

$parser = ChangelogParser::parseMarkdownFile('changelog.md');

$latest = $parser->getLatestVersion();
```

### Get a version by number

```php
use Mistralys\ChangelogParser\ChangelogParser;

$parser = ChangelogParser::parseMarkdownFile('changelog.md');

$version = $parser->getVersionByNumber('5.2.0');
```

This will throw an exception if the version is not found. To check if a
version number exists beforehand

### Check if a version exists

```php
use Mistralys\ChangelogParser\ChangelogParser;

$parser = ChangelogParser::parseMarkdownFile('changelog.md');

if($parser->versionExists('5.2.0'))
{
    $version = $parser->getVersionByNumber('5.2.0');
}
```

Note that this requires the exact version number to be known (major, minor 
and patch version numbers). For a more flexible way to find versions, the 
version info is best used instead. 

For example, to find all versions matching `v4.2.x`:

```php
use Mistralys\ChangelogParser\ChangelogParser;

$versions = ChangelogParser::parseMarkdownFile('changelog.md')->getVersions();

foreach($versions as $version)
{
    $info = $version->getVersionInfo();
    
    if($info->getMajorVersion() === 4 && $info->getMinorVersion() === 2) 
    {
        // Matches v4.2
    }
}
```

### Go through individual changes in a version

```php
use Mistralys\ChangelogParser\ChangelogParser;

$version = ChangelogParser::parseMarkdownFile('changelog.md')->requireLatestVersion();

$changes = $version->getChanges();

echo "Changes in version ".$version->getNumber().":".PHP_EOL;

foreach($changes as $change)
{
    echo '- '.$change->getText().PHP_EOL;
}
```

> Note the use of the `requireLatestVersion()` method: This will throw
> an exception instead of `NULL` if no versions are found in the 
> change log. Handy to avoid checking for a null value.
 
## Persisting and caching

To easily store or transmit changelog information, the parser offers the
possibility to serialize the data to JSON. This can be decoded again later
instead of parsing the source file each time.

```php
use Mistralys\ChangelogParser\ChangelogParser;

$json = ChangelogParser::parseMarkdownFile('changelog.md')->toJSON();
```

> TIP: Loading changelog information from serialized JSON performs
> better than parsing the source markdown file, especially for large
> files. It is recommended to use this as a way of caching the information.
