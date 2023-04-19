<?php

declare(strict_types=1);

namespace Mistralys\ChangelogParserTests\TestSuites;

use Mistralys\ChangelogParser\ChangelogParser;
use Mistralys\ChangelogParser\Reader\JSONChangelogReader;
use Mistralys\ChangelogParserTests\TestClasses\ChangelogParserTestCase;

final class PersistenceTests extends ChangelogParserTestCase
{
    public function test_restore() : void
    {
        $parser = $this->createTestChangelogParser();

        $json = $parser->toJSON();

        $restored = new ChangelogParser(JSONChangelogReader::createFromString($json));

        $this->assertSame($parser->getVersionNumbers(), $restored->getVersionNumbers());

        $lastOrig = $parser->getLatestVersion();
        $lastRestored = $restored->getLatestVersion();

        $this->assertNotNull($lastOrig);
        $this->assertNotNull($lastRestored);

        $this->assertSame($lastOrig->getNumber(), $lastRestored->getNumber());

        $origChanges = $lastOrig->getChanges();
        $restoredChanges = $lastRestored->getChanges();

        $this->assertNotEmpty($origChanges);

        foreach($origChanges as $idx => $origChange)
        {
            $this->assertArrayHasKey($idx, $restoredChanges);
            $restoredChange = $restoredChanges[$idx];

            $this->assertSame($origChange->getText(), $restoredChange->getText());
            $this->assertSame($origChange->getCategory(), $restoredChange->getCategory());
        }
    }
}
