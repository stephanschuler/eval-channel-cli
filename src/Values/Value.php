<?php
declare(strict_types=1);

namespace StephanSchuler\EvalChannelCli\Values;

abstract class Value
{
    protected $value;

    private function __construct($value)
    {
        $this->value = $value;
    }

    public static function lazy(callable $callable): self
    {
        return new Lazy($callable);
    }

    public static function instant(string $value): self
    {
        return new Instant($value);
    }

    abstract public function getValue(): string;

    final public function __toString(): string
    {
        return $this->getValue();
    }
}
