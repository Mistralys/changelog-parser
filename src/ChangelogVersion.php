<?php

declare(strict_types=1);

namespace Mistralys\ChangelogParser;

use Mistralys\ChangelogParser\Changes\SubHeader;
use Mistralys\VersionParser\VersionParser;

class ChangelogVersion
{
    public const ERROR_MISSING_SERIALIZED_KEYS = 133501;
    public const ERROR_INVALID_SERIALIZED_KEY_TYPES = 133502;

    public const SERIALIZED_NUMBER = 'number';
    public const SERIALIZED_CHANGES = 'changes';


    private VersionParser $versionInfo;
    private int $level;
    private string $label;

    /**
     * @var BaseChangeEntry[]
     */
    private array $changes = array();

    /**
     * @var array<int,string|SubHeader>
     */
    private array $freeformLines = array();

    public function __construct(VersionParser $version, string $label='', int $level=1)
    {
        $this->versionInfo = $version;
        $this->level = $level;
        $this->label = $label;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getVersionInfo() : VersionParser
    {
        return $this->versionInfo;
    }

    /**
     * @return BaseChangeEntry[]
     */
    public function getChanges() : array
    {
        return $this->changes;
    }


    public function addChange(BaseChangeEntry $change) : self
    {
        $this->changes[] = $change;
        return $this;
    }

    public function addTextLine(string $line) : self
    {
        $this->freeformLines[] = $line;
        return $this;
    }

    public function addSubHeader(SubHeader $subHeader) : self
    {
        $this->freeformLines[] = $subHeader;
        return $this;
    }

    public function getFreeformText() : string
    {
        return trim(implode(PHP_EOL, $this->freeformLines));
    }

    public function getNumber() : string
    {
        return $this->getVersionInfo()->getVersion();
    }

    // region: Persistence

    /**
     * @return array{number:string,changes:array<int,array<string,mixed>>}
     */
    public function toArray() : array
    {
        $result = array(
            self::SERIALIZED_NUMBER => $this->getNumber(),
            self::SERIALIZED_CHANGES => array()
        );

        $changes = $this->getChanges();
        foreach($changes as $change)
        {
            $result[self::SERIALIZED_CHANGES][] = $change->toArray();
        }

        return $result;
    }

    /**
     * @param array<mixed> $data
     * @throws ChangelogParserException
     */
    public static function fromArray(array $data) : ChangelogVersion
    {
        if(!isset($data[self::SERIALIZED_NUMBER], $data[self::SERIALIZED_CHANGES]))
        {
            throw new ChangelogParserException(
                'Missing keys in serialized data',
                sprintf(
                    'Looking for [%s], keys present: [%s].',
                    implode(', ', array(self::SERIALIZED_NUMBER, self::SERIALIZED_CHANGES)),
                    implode(', ', array_keys($data))
                ),
                self::ERROR_MISSING_SERIALIZED_KEYS
            );
        }

        $number = $data[self::SERIALIZED_NUMBER];
        $changes = $data[self::SERIALIZED_CHANGES];

        if(!is_string($number) || !is_array($changes)) {
            throw new ChangelogParserException(
                'Invalid key data types in serialized data',
                '',
                self::ERROR_INVALID_SERIALIZED_KEY_TYPES
            );
        }

        $version = new ChangelogVersion(VersionParser::create($number));

        foreach($changes as $change)
        {
            $version->addChange(BaseChangeEntry::fromArray($change));
        }

        return $version;
    }

    // endregion
}
