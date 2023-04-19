<?php

declare(strict_types=1);

namespace Mistralys\ChangelogParser\Changes;

use AppUtils\Interface_Stringable;

class SubHeader implements Interface_Stringable
{
    private int $level;

    /**
     * @var string[]
     */
    private array $lines = array();
    private string $label;

    public function __construct(int $level, string $label)
    {
        $this->level = $level;
        $this->label = $label;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getBodyText() : string
    {
        return trim(implode(PHP_EOL, $this->lines));
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function addTextLine(string $line) : void
    {
        $this->lines[] = $line;
    }

    public function render() : string
    {
        return trim(
            str_repeat('#', $this->level).' '.$this->label.PHP_EOL.
            $this->getBodyText()
        );
    }

    public function __toString() : string
    {
        return $this->render();
    }
}
