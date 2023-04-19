<?php

declare(strict_types=1);

namespace Mistralys\ChangelogParserTests\TestSuites;

use Mistralys\ChangelogParser\Changes\ContentChange;
use Mistralys\ChangelogParser\Changes\GlobalChange;
use Mistralys\ChangelogParser\Changes\MiscChange;
use Mistralys\ChangelogParserTests\TestClasses\ChangelogParserTestCase;

final class ParsingTests extends ChangelogParserTestCase
{
    public function test_parse() : void
    {
        $versions = $this->createTestChangelogParser()->getVersions();

        $this->assertCount(8, $versions);
    }

    public function test_lastVersion() : void
    {
        $version = $this->createTestChangelogParser()->getLatestVersion();

        $this->assertNotNull($version);

        $this->assertSame('1.1.6', $version->getVersionInfo()->getVersion());
    }

    public function test_getVersionByNumber() : void
    {
        $version = $this->createTestChangelogParser()->getVersionByNumber('1.1.5');

        $this->assertSame('1.1.5', $version->getNumber());
        $this->assertSame(3, $version->getLevel());
    }

    public function test_getChanges() : void
    {
        $changes = $this->createTestChangelogParser()->getVersionByNumber('1.1.5')->getChanges();

        $this->assertCount(8, $changes);

        [
            $contentOptional,
            $contentMandatory,
            $globalMandatory,
            $globalOptional,
            $neutral
        ] = $changes;

        $this->assertInstanceOf(ContentChange::class, $contentOptional);
        $this->assertInstanceOf(ContentChange::class, $contentMandatory);
        $this->assertInstanceOf(GlobalChange::class, $globalOptional);
        $this->assertInstanceOf(GlobalChange::class, $globalMandatory);
        $this->assertInstanceOf(MiscChange::class, $neutral);

        $this->assertTrue($contentOptional->isOptional());
        $this->assertTrue($contentMandatory->isMandatory());
        $this->assertTrue($globalOptional->isOptional());
        $this->assertTrue($globalMandatory->isMandatory());
        $this->assertTrue($neutral->isNeutral());
    }

    public function test_changes() : void
    {
        $changes = $this->createTestChangelogParser()->getVersionByNumber('1.1.5')->getChanges();

        $this->assertNotEmpty($changes);

        $change = $changes[0];
        $this->assertSame('ServiceBlock', $change->getCategory());
        $this->assertSame('Implemented the contact options.', $change->getText());

        $this->assertArrayHasKey(4, $changes);
        $this->assertStringContainsString('https://mistralys.eu', $changes[4]->getText());
    }
}
