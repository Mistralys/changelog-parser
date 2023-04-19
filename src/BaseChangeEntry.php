<?php

declare(strict_types=1);

namespace Mistralys\ChangelogParser;

use AppUtils\ClassHelper;
use Mistralys\ChangelogParser\Changes\ContentChange;
use Mistralys\ChangelogParser\Changes\GlobalChange;
use Mistralys\ChangelogParser\Changes\MiscChange;

abstract class BaseChangeEntry
{
    public const ERROR_UNKNOWN_CHANGE_CLASS = 123601;

    public const CHANGE_NEUTRAL = 'neutral';
    public const CHANGE_OPTIONAL = 'optional';
    public const CHANGE_MANDATORY = 'mandatory';

    public const SERIALIZED_ID = 'id';
    public const SERIALIZED_TYPE = 'type';
    public const SERIALIZED_CATEGORY = 'category';
    public const SERIALIZED_TEXT = 'text';

    public const LETTER_CONTENT = 'C';
    public const LETTER_GLOBAL = 'G';
    public const LETTER_EMPTY = ' ';

    public const CHAR_OPTION = '{';
    public const CHAR_MANDATORY = '(';

    private string $category;
    private string $text;
    private string $type;

    public function __construct(string $type, string $category, string $text)
    {
        $this->type = $type;
        $this->category = $category;
        $this->text = $text;
    }

    public function isOptional() : bool
    {
        return $this->getType() === self::CHANGE_OPTIONAL;
    }

    public function isMandatory() : bool
    {
        return $this->getType() === self::CHANGE_MANDATORY;
    }

    public function isNeutral() : bool
    {
        return $this->getType() === self::CHANGE_NEUTRAL;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return array{id:string,type:string,category:string,text:string}
     */
    public function toArray() : array
    {
        return array(
            self::SERIALIZED_ID => ClassHelper::getClassTypeName($this),
            self::SERIALIZED_TYPE => $this->getType(),
            self::SERIALIZED_CATEGORY => $this->getCategory(),
            self::SERIALIZED_TEXT => $this->getText()
        );
    }

    /**
     * @param array{id:string,type:string,category:string,text:string} $data
     * @return BaseChangeEntry
     * @throws ChangelogParserException
     */
    public static function fromArray(array $data) : BaseChangeEntry
    {
        switch($data[self::SERIALIZED_ID])
        {
            case ClassHelper::getClassTypeName(ContentChange::class):
                return new ContentChange(
                    $data[self::SERIALIZED_TYPE],
                    $data[self::SERIALIZED_CATEGORY],
                    $data[self::SERIALIZED_TEXT]
                );

            case ClassHelper::getClassTypeName(GlobalChange::class):
                return new GlobalChange(
                    $data[self::SERIALIZED_TYPE],
                    $data[self::SERIALIZED_CATEGORY],
                    $data[self::SERIALIZED_TEXT]
                );

            case ClassHelper::getClassTypeName(MiscChange::class):
                return new MiscChange(
                    $data[self::SERIALIZED_TYPE],
                    $data[self::SERIALIZED_CATEGORY],
                    $data[self::SERIALIZED_TEXT]
                );
        }

        throw new ChangelogParserException(
            'Unhandled change entry class.',
            sprintf(
                'The ID [%s] does not match any known change classes.',
                $data[self::SERIALIZED_ID]
            ),
            self::ERROR_UNKNOWN_CHANGE_CLASS
        );
    }

    /**
     * @throws ChangelogParserException
     */
    public static function fromChangelog(string $char, string $letter, string $category, string $text) : BaseChangeEntry
    {
        return self::fromArray(array(
            self::SERIALIZED_ID => self::resolveID($letter),
            self::SERIALIZED_TYPE => self::resolveType($char, $letter),
            self::SERIALIZED_CATEGORY => $category,
            self::SERIALIZED_TEXT => $text
        ));
    }

    private static function resolveID(string $letter) : string
    {
        if($letter === self::LETTER_CONTENT) {
            return ClassHelper::getClassTypeName(ContentChange::class);
        }

        if($letter === self::LETTER_GLOBAL) {
            return ClassHelper::getClassTypeName(GlobalChange::class);
        }

        return ClassHelper::getClassTypeName(MiscChange::class);
    }

    private static function resolveType(string $char, string $letter) : string
    {
        if($char === self::CHAR_OPTION) {
            return self::CHANGE_OPTIONAL;
        }

        if($char === self::CHAR_MANDATORY && $letter === self::LETTER_EMPTY) {
            return self::CHANGE_NEUTRAL;
        }

        if($char === self::CHAR_MANDATORY) {
            return self::CHANGE_MANDATORY;
        }

        return self::CHANGE_NEUTRAL;
    }
}
