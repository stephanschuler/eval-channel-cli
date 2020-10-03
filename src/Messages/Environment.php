<?php
declare(strict_types=1);

namespace StephanSchuler\EvalChannelCli\Messages;

use StephanSchuler\EvalChannelCli\ConsoleAdapter;
use StephanSchuler\EvalChannelCli\Shell;
use StephanSchuler\EvalChannelCli\Values\Value;

final class Environment implements Message
{
    private $name;
    private $value;

    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public static function introduce(string $name): self
    {
        return new static($name, '');
    }

    public function withValue(Value $value): self
    {
        return new static($this->name, $value);
    }

    public function execute(Shell $shell, ConsoleAdapter $console): void
    {
        $externalAssignment = sprintf(
            'export %s=%s',
            escapeshellarg((string)$this->name),
            escapeshellarg((string)$this->value)
        );
        $shell->protocol('export ' . $externalAssignment);

        $internalAssignment = sprintf(
            '%s=%s',
            $this->name,
            $this->value
        );
        putenv($internalAssignment);
    }
}
