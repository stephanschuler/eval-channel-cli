<?php
declare(strict_types=1);

namespace StephanSchuler\EvalChannelCli\Messages;

use StephanSchuler\EvalChannelCli\ConsoleAdapter;
use StephanSchuler\EvalChannelCli\Stream\Streams;

final class Terminate implements Message
{
    private $statuscode = 0;

    public function __construct(int $statuscode)
    {
        $this->statuscode = $statuscode;
    }

    public static function successfully(): self
    {
        return new static(0);
    }

    public static function withError(int $statuscode): self
    {
        return new static($statuscode);
    }

    public function execute(Streams $streams, ConsoleAdapter $console): void
    {
        $command = sprintf('return %d', $this->statuscode);
        $command = 'break 1';
        $streams->protocol($command);
    }
}
