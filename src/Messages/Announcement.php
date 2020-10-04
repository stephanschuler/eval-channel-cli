<?php
declare(strict_types=1);

namespace StephanSchuler\EvalChannelCli\Messages;

use StephanSchuler\EvalChannelCli\ConsoleAdapter;
use StephanSchuler\EvalChannelCli\Stream\Streams;
use StephanSchuler\EvalChannelCli\Values\Value;

final class Announcement implements Message
{
    private const STDOUT = '>&1';
    private const STDERR = '>&2';

    private $value;
    private $withNewline;
    private $stream;
    private $arguments;

    public function __construct(string $value, bool $withNewline, string $stream, array $arguments)
    {
        $this->value = $value;
        $this->withNewline = $withNewline;
        $this->stream = $stream;
        $this->arguments = $arguments;
    }

    public static function notify(string $value): self
    {
        return new static($value, true, self::STDOUT, []);
    }

    public static function warn(string $value): self
    {
        return new static($value, true, self::STDERR, []);
    }

    public function withoutNewline(): self
    {
        return new static($this->value, false, $this->stream, $this->arguments);
    }

    public function withNewline(): self
    {
        return new static($this->value, true, $this->stream, $this->arguments);
    }

    public function withArgument(Value $argument): self
    {
        $arguments = $this->arguments;
        $arguments[] = $argument;
        return new static($this->value, $this->withNewline, $this->stream, $arguments);
    }

    public function execute(Streams $streams, ConsoleAdapter $console): void
    {
        $value = $this->value;
        if ($this->withNewline) {
            $value .= PHP_EOL;
        }
        if (count($this->arguments)) {
            $value = vsprintf($value, $this->arguments);
        }
        switch ($this->stream) {
            case self::STDOUT:
                $streams->stdout($value);
                break;
            case self::STDERR:
                $streams->stderr($value);
                break;
            default:
                throw new \Exception('Invalid stream');
        }
    }
}
