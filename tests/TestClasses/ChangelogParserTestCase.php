<?php

declare(strict_types=1);

namespace Mistralys\ChangelogParserTests\TestClasses;

use AppUtils\FileHelper\FileInfo;
use Mistralys\ChangelogParser\ChangelogParser;
use Mistralys\ChangelogParser\Reader\MarkdownChangelogReader;
use PHPUnit\Framework\TestCase;

abstract class ChangelogParserTestCase extends TestCase
{
    protected string $fileStandard;
    protected string $fileCategorized;

    protected function setUp(): void
    {
        $this->fileCategorized = __DIR__.'/../files/changelog-categorized.md';
        $this->fileStandard = __DIR__.'/../files/changelog-standard.md';
    }
    public function createTestChangelogParser() : ChangelogParser
    {
        return new ChangelogParser(MarkdownChangelogReader::create(
            FileInfo::factory($this->fileCategorized)
        ));
    }

    public function createStandardChangelogParser() : ChangelogParser
    {
        return new ChangelogParser(MarkdownChangelogReader::create(
            FileInfo::factory($this->fileStandard)
        ));
    }
}
