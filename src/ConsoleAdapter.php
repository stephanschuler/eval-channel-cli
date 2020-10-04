<?php
declare(strict_types=1);

namespace StephanSchuler\EvalChannelCli;

use StephanSchuler\EvalChannelCli\Messages\Message;

final class ConsoleAdapter
{
    private $stdout;
    private $stderr;
    private $protocol;
    private $shell;

    public function __construct()
    {
        $this->stdout = Stream\Stream::fromStream('php://fd/1');
        $this->stderr = Stream\Stream::fromStream('php://fd/2');
        $this->protocol = Stream\Stream::fromStream('php://fd/3')
            ->transform(static function (string $message) {
                return base64_encode($message) . PHP_EOL;
            });

        $this->shell = new Shell(
            $this->stdout,
            $this->stderr,
            $this->protocol
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
            $message->execute($this->shell, $this);
        }
        return $this;
    }
}
