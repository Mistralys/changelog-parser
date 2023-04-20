<?php

declare(strict_types=1);

namespace Mistralys\ChangelogParserTests\TestSuites;

use Mistralys\ChangelogParser\BaseChangeEntry;
use Mistralys\ChangelogParserTests\TestClasses\ChangelogParserTestCase;

final class StandardParsingTests extends ChangelogParserTestCase
{
    public function test_parse() : void
    {
        $parser = $this->createStandardChangelogParser();
        $versions = $parser->getVersions();

        $this->assertCount(6, $versions);

        $this->assertSame(
            array(
                '4.0.0-alpha',
                '14.5.9-snapshot5',
                '14.5.8',
                '14.5.7',
                '14.5.7-beta2',
                '14.5.6'
            ),
            $parser->getVersionNumbers()
        );
    }

    public function test_lastVersion() : void
    {
        $version = $this->createStandardChangelogParser()->getLatestVersion();

        $this->assertNotNull($version);

        $this->assertSame('4.0.0', $version->getVersionInfo()->getVersion());
    }

    public function test_getVersionByNumber() : void
    {
        $version = $this->createStandardChangelogParser()->getVersionByNumber('14.5.7');

        $this->assertSame('14.5.7', $version->getNumber());
    }

    public function test_changes() : void
    {
        $changes = $this->createStandardChangelogParser()->getVersionByNumber('14.5.7')->getChanges();

        $this->assertCount(3, $changes);

        $this->assertTrue($changes[0]->isNeutral());

        $this->assertSame('Other category', $changes[0]->getCategory());
        $this->assertSame('Some text', $changes[0]->getText());
        $this->assertSame('', $changes[1]->getCategory());
        $this->assertSame('Change without category', $changes[1]->getText());
    }

    public function test_ignoreNonVersionSubHeadings() : void
    {
        $expectedText = <<<'EOT'
### Sub-heading of the version
- Item will be ignored

This text will also be added to the subheader.
EOT;

        $version = $this->createStandardChangelogParser()->getVersionByNumber('14.5.6');
        $changes = $version->getChanges();

        $this->assertCount(1, $changes, $this->dumpChanges($changes));
        $this->assertSame($expectedText, $version->getFreeformText());
    }

    /**
     * @param BaseChangeEntry[] $changes
     * @return string
     */
    private function dumpChanges(array $changes) : string
    {
        $result = array();

        foreach($changes as $idx => $change)
        {
            $result[] = sprintf(
                'Change #%4$s'.PHP_EOL.
                'Type: %1$s'.PHP_EOL.
                'Category: %2$s'.PHP_EOL.
                'Text: %3$s',
                $change->getType(),
                $change->getCategory(),
                $change->getText(),
                $idx+1
            );
        }

        return implode(PHP_EOL.PHP_EOL, $result);
    }
}
