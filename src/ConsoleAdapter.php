<?php
declare(strict_types=1);

namespace StephanSchuler\EvalChannelCli;

use StephanSchuler\EvalChannelCli\Messages\Message;
use StephanSchuler\EvalChannelCli\Stream\Streams;

final class ConsoleAdapter
{
    private $streams;

    public function __construct()
    {
        $stdout = Stream\Stream::fromResource(STDOUT);
        $stderr = Stream\Stream::fromResource(STDERR);
        $protocol = Stream\Stream::fromResource(fopen('php://fd/3', 'w'))
            ->transform(static function (string $message) {
                return base64_encode($message) . PHP_EOL;
            });

        $this->streams = new Streams(
            $stdout,
            $stderr,
            $protocol
        );
    }

    public static function open(): self
    {
        return new static();
    }

    public function send(Message $message, Message ...$messages): self
    {
        foreach (func_get_args() as $message) {
            assert($message instanceof Message);
            $message->execute($this->streams, $this);
        }
        return $this;
    }
}
