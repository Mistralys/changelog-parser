<?php

declare(strict_types=1);

namespace Mistralys\ChangelogParser\Reader;

use AppUtils\FileHelper\FileInfo;
use Mistralys\ChangelogParser\BaseChangeEntry;
use Mistralys\ChangelogParser\BaseReader;
use Mistralys\ChangelogParser\ChangelogVersion;
use Mistralys\ChangelogParser\Changes\SubHeader;
use Mistralys\VersionParser\VersionParser;

class MarkdownChangelogReader extends BaseReader
{
    public const ERROR_CHANGELOG_FILE_NOT_FOUND = 134701;

    /**
     * @var ChangelogVersion[]
     */
    private array $versions = array();
    private ?ChangelogVersion $activeVersion;
    private ?SubHeader $activeSubHeader = null;
    private int $level = -1;

    public function __construct(FileInfo $changelogFile)
    {
        parent::__construct($changelogFile);

        if(!$changelogFile->exists()) {
            $this->makeError(
                'The changelog file could not be found.',
                self::ERROR_CHANGELOG_FILE_NOT_FOUND
            );
            return;
        }

        $lines = explode("\n", $changelogFile->getContents());

        foreach($lines as $line)
        {
            $this->parseLine(trim($line));
        }
    }

    public static function create(FileInfo $changelogFile) : MarkdownChangelogReader
    {
        return new MarkdownChangelogReader($changelogFile);
    }

    /**
     * @return ChangelogVersion[]
     */
    public function getVersions(): array
    {
        return $this->versions;
    }

    private function parseLine(string $line) : void
    {
        $level = null;
        $workLine = $line;

        // Only do this if it's not an empty line
        if($workLine !== '')
        {
            if (strpos($workLine, '#') === 0) {
                $parts = explode(' ', $workLine);
                $level = substr_count(array_shift($parts), '#');
                $workLine = trim(implode(' ', $parts));
            }

            if ($level !== null && $this->parseHeader($level, $workLine)) {
                return;
            }
        }

        // Ignore anything not nested in a version
        if(!isset($this->activeVersion)) {
            return;
        }

        // If a subheader is active, add the line to it
        if(isset($this->activeSubHeader)) {
            $this->activeSubHeader->addTextLine($workLine);
            return;
        }

        // Is it a change entry for the active version?
        if($workLine !== '' && strpos($workLine, '- ') === 0) {
            $this->parseEntry($workLine, $this->activeVersion);
            return;
        }

        // Treat it as a misc line of text.
        $this->activeVersion->addTextLine($workLine);
    }

    private function parseHeader(int $level, string $line) : bool
    {
        preg_match('/v?([0-9.]+[0-9A-Z._\-]*) (.*)|v?([0-9.]+[0-9A-Z._\-]*)/six', $line, $matches);

        // Detect a version line, regardless of the amount of hashes
        if(!empty($matches[0]))
        {
            $version = $matches[1];
            if(!empty($matches[3])) {
                $version = $matches[3];
            }

            $versionInfo = VersionParser::create($version);

            // Is this a valid version string?
            if ($versionInfo->getBuildNumberInt() > 0)
            {
                // Store the level
                if ($this->level === -1) {
                    $this->level = $level;
                }

                $label = trim(ltrim($matches[2], '-~|'));
                $version = new ChangelogVersion($versionInfo, $label, $level);

                $this->activeVersion = $version;
                $this->versions[] = $version;
                $this->activeSubHeader = null;
                return true;
            }
        }

        // Non-version header at the same or higher level than the
        // changelog versions: Stop the capturing of data.
        if ($level <= $this->level) {
            $this->activeVersion = null;
            $this->activeSubHeader = null;
        }

        // This is a subheader of the active version
        if(isset($this->activeVersion)) {
             $this->parseSubHeader($this->activeVersion, $level, $line);
             return true;
        }

        return false;
    }

    private function parseSubHeader(ChangelogVersion $version, int $level, string $label) : void
    {
        $subHeader = new SubHeader($level, $label);
        $this->activeSubHeader = $subHeader;

        $version->addSubHeader($subHeader);
    }

    /**
     * @param string $line
     * @param ChangelogVersion $version
     * @return void
     * @throws ChangelogParserException
     */
    private function parseEntry(string $line, ChangelogVersion $version) : void
    {
        preg_match_all('/\A- ([{(])([CG ])([})])([^:]*):(.*)\z/i', $line, $matches);

        // Entry with brackets, e.g.
        // - {G} Category: Text
        if(isset($matches[0][0]))
        {
            $version->addChange(BaseChangeEntry::fromChangelog(
                $matches[1][0], // Bracket character
                $matches[2][0], // Letter
                trim($matches[4][0]), // Category
                trim($matches[5][0]) // Text
            ));

            return;
        }

        $line = trim($line, '- ');

        // Entry without brackets, e.g.
        // - Category: Text
        if(strpos($line, ':') !== false)
        {
            $parts = explode(':', $line);
            $category = trim(array_shift($parts));
            $text = trim(implode(':', $parts));

            $version->addChange(BaseChangeEntry::fromChangelog(
                '',
                BaseChangeEntry::LETTER_EMPTY,
                $category,
                $text
            ));

            return;
        }

        $version->addChange(BaseChangeEntry::fromChangelog(
            '',
            BaseChangeEntry::LETTER_EMPTY,
            '',
            $line
        ));
    }
}
