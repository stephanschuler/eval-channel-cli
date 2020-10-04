<?php
declare(strict_types=1);

namespace StephanSchuler\EvalChannelCli\Messages;

use StephanSchuler\EvalChannelCli\ConsoleAdapter;
use StephanSchuler\EvalChannelCli\Stream\Streams;
use StephanSchuler\EvalChannelCli\Values\Value;

final class Question implements Message
{
    private const STDIN = 'php://stdin';

    private $question;
    private $response;

    public function __construct(string $question, string ...$response)
    {
        $this->question = $question;
        $this->response = $response;
    }

    public static function askFor(string $question): self
    {
        return new static($question);
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    public function as(Value &$variable = null): self
    {
        $variable = Value::lazy(function () {
            return join(PHP_EOL, $this->response);
        });
        return $this;
    }

    public function execute(Streams $streams, ConsoleAdapter $console): void
    {
        $this->ask($console);
        $this->capture($streams);
    }

    protected function ask(ConsoleAdapter $console): void
    {
        $console->send(
            Announcement::notify($this->question . ' ')
                ->withoutNewline()
        );
    }

    protected function capture(Streams $shell): void
    {
        $stdin = fopen(self::STDIN, 'r');
        $this->response = [];
        while ($stdin && ($line = fgets($stdin)) != false) {
            $this->response[] = substr($line, 0, -1);
            break;
        }
        fclose($stdin);
    }
}
