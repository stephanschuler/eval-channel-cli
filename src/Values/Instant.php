<?php
declare(strict_types=1);

namespace StephanSchuler\EvalChannelCli\Values;

final class Instant extends Value
{
    /** @var string */
    protected $value;

    public function __toString(): string
    {
        return $this->value;
    }
}
