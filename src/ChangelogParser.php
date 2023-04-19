<?php
/**
 * @package Changelog Parser
 * @subpackage Parser
 * @see \Mistralys\ChangelogParser\ChangelogParser
 */

declare(strict_types=1);

namespace Mistralys\ChangelogParser;

use AppUtils\FileHelper\FileInfo;
use AppUtils\FileHelper_Exception;
use AppUtils\OperationResult;
use JsonException;
use Mistralys\ChangelogParser\Reader\MarkdownChangelogReader;

/**
 * Main entry point for parsing changelog files. Use the
 * {@see self::parseMarkdownFile()} method for example to
 * create a new instance and access changelog information.
 *
 * @package Changelog Parser
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ChangelogParser extends OperationResult
{
    public const ERROR_NO_LAST_VERSION_AVAILABLE = 123101;
    public const ERROR_UNKNOWN_VERSION_NUMBER = 123102;

    /**
     * @var ChangelogVersion[]
     */
    private array $versions;

    public function __construct(BaseReader $reader)
    {
        $this->versions = $reader->getVersions();

        parent::__construct($reader);

        if(!$reader->isValid()) {
            $this->makeError($reader->getErrorMessage(), $reader->getCode());
        }
    }

    /**
     * @param string|FileInfo $file
     * @return ChangelogParser
     * @throws FileHelper_Exception
     */
    public static function parseMarkdownFile($file) : ChangelogParser
    {
        return new self(MarkdownChangelogReader::create(FileInfo::factory($file)));
    }

    /**
     * @return ChangelogVersion[]
     */
    public function getVersions() : array
    {
        return $this->versions;
    }

    /**
     * @return ChangelogVersion|null
     */
    public function getLatestVersion() : ?ChangelogVersion
    {
        $versions = $this->getVersions();

        if(!empty($versions)) {
            return $versions[key($versions)];
        }

        return null;
    }

    /**
     * @return ChangelogVersion
     *
     * @throws ChangelogParserException
     */
    public function requireLatestVersion() : ChangelogVersion
    {
        $version = $this->getLatestVersion();
        if($version !== null) {
            return $version;
        }

        throw new ChangelogParserException(
            'No last version available in the changelog.',
            '',
            self::ERROR_NO_LAST_VERSION_AVAILABLE
        );
    }

    /**
     * @param string $version Full version string, e.g. "4.5.2" (Major, minor and path version numbers required)
     * @return bool
     */
    public function versionExists(string $version) : bool
    {
        return in_array($version, $this->getVersionNumbers(), true);
    }

    /**
     * @param string $number Full version string, e.g. "4.5.2" (Major, minor and path version numbers required)
     * @return ChangelogVersion
     *
     * @throws ChangelogParserException
     */
    public function getVersionByNumber(string $number) : ChangelogVersion
    {
        $versions = $this->getVersions();

        foreach($versions as $version)
        {
            if($version->getNumber() === $number)
            {
                return $version;
            }
        }

        throw new ChangelogParserException(
            'No such version found.',
            sprintf(
                'The version number [%s] does not exist. Available numbers are [%s].',
                $number,
                implode(', ', $this->getVersionNumbers())
            ),
            self::ERROR_UNKNOWN_VERSION_NUMBER
        );
    }

    /**
     * @return string[] Full version strings, e.g. "4.5.2" (Major, minor and path version numbers included)
     */
    public function getVersionNumbers() : array
    {
        $result = array();
        $versions = $this->getVersions();

        foreach($versions as $version)
        {
            $result[] = $version->getNumber();
        }

        return $result;
    }

    // region: Persistence

    /**
     * @throws JsonException
     */
    public function toJSON() : string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function toArray() : array
    {
        $versions = $this->getVersions();
        $result = array();

        foreach($versions as $version)
        {
            $result[] = $version->toArray();
        }

        return $result;
    }

    // endregion
}
