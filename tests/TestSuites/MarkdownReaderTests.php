<?php

declare(strict_types=1);

namespace Mistralys\ChangelogParserTests\TestSuites;

use AppUtils\FileHelper\FileInfo;
use Mistralys\ChangelogParser\ChangelogParser;
use Mistralys\ChangelogParser\Reader\MarkdownChangelogReader;
use Mistralys\ChangelogParserTests\TestClasses\ChangelogParserTestCase;

final class MarkdownReaderTests extends ChangelogParserTestCase
{
    public function test_readFromFilePathString() : void
    {
        $parser = ChangelogParser::parseMarkdownFile($this->fileStandard);

        $this->assertNotEmpty($parser->getVersions());
    }

    public function test_readFromFileInfoInstance() : void
    {
        $parser = ChangelogParser::parseMarkdownFile(FileInfo::factory($this->fileStandard));

        $this->assertNotEmpty($parser->getVersions());
    }

    public function test_readFromUnknownFile() : void
    {
        $parser = ChangelogParser::parseMarkdownFile(FileInfo::factory('/unknown-changelog-file'));

        $this->assertFalse($parser->isValid());
        $this->assertSame(MarkdownChangelogReader::ERROR_CHANGELOG_FILE_NOT_FOUND, $parser->getCode());
    }
}