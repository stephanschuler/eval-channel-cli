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
        $protocolTransformation = function (string $message) {
            return base64_encode($message) . PHP_EOL;
        };
        $this->stdout = Stream\Stream::fromStream('php://fd/3')
            ->transform(function (string $message) {
                return '>&1 echo -n ' . escapeshellarg($message);
            })
            ->transform($protocolTransformation);

        $this->stderr = Stream\Stream::fromStream('php://fd/3')
            ->transform(function (string $message) {
                return '>&2 echo -n ' . escapeshellarg($message);
            })
            ->transform($protocolTransformation);

        $this->protocol = Stream\Stream::fromStream('php://fd/3')
            ->transform($protocolTransformation);

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
