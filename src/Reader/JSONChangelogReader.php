<?php

declare(strict_types=1);

namespace Mistralys\ChangelogParser\Reader;

use AppUtils\FileHelper\JSONFile;
use AppUtils\FileHelper_Exception;
use JsonException;
use Mistralys\ChangelogParser\BaseReader;
use Mistralys\ChangelogParser\ChangelogParserException;
use Mistralys\ChangelogParser\ChangelogVersion;

class JSONChangelogReader extends BaseReader
{
    public const ERROR_INVALID_JSON_DATA = 123701;

    /**
     * @var ChangelogVersion[]
     */
    private array $versions = array();

    /**
     * @param array<mixed> $data
     * @throws ChangelogParserException
     * @throws JsonException
     * @throws ChangelogParserException
     */
    private function __construct(array $data)
    {
        foreach($data as $version)
        {
            if(is_array($version)) {
                $this->versions[] = ChangelogVersion::fromArray($version);
            }
        }
    }

    public function getVersions(): array
    {
        return $this->versions;
    }

    /**
     * @throws FileHelper_Exception
     * @throws JsonException
     */
    public static function createFromFile(JSONFile $jsonFile) : JSONChangelogReader
    {
        return new JSONChangelogReader($jsonFile->parse());
    }

    /**
     * @throws JsonException
     * @throws ChangelogParserException
     */
    public static function createFromString(string $json) : JSONChangelogReader
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if(!is_array($data)) {
            throw new ChangelogParserException(
                'Invalid JSON data.',
                'Did not decode into an array.',
                self::ERROR_INVALID_JSON_DATA
            );
        }

        return self::createFromArray($data);
    }

    /**
     * @param array<mixed> $data
     * @throws ChangelogParserException
     * @throws ChangelogParserException
     * @throws JsonException
     */
    public static function createFromArray(array $data) : JSONChangelogReader
    {
        return new JSONChangelogReader($data);
    }
}
