<?php
declare(strict_types=1);

namespace StephanSchuler\EvalChannelCli\Values;

final class Lazy extends Value
{
    /** @var callable */
    protected $value;

    public function __toString(): string
    {
        return ($this->value)();
    }
}
