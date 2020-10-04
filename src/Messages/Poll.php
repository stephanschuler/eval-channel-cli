<?php
declare(strict_types=1);

namespace StephanSchuler\EvalChannelCli\Messages;

use StephanSchuler\EvalChannelCli\ConsoleAdapter;
use StephanSchuler\EvalChannelCli\Stream\Streams;
use StephanSchuler\EvalChannelCli\Values\Value;

final class Poll implements Message
{
    private const STDIN = 'php://stdin';

    private $question;
    private $options;
    private $response;

    public function __construct(string $question, string ...$options)
    {
        $this->question = $question;
        $this->options = $options;
        $this->response = '';
    }

    public static function askFor(string $question): self
    {
        return new static($question);
    }

    public function withOption(string $option): self
    {
        return new static($this->question, $option, ... $this->options);
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function as(Value &$variable = null): self
    {
        $variable = Value::lazy(function () {
            return $this->response;
        });
        return $this;
    }

    public function execute(Streams $streams, ConsoleAdapter $console): void
    {
        do {
            $console->send(
                Question::askFor($this->question . ' [' . join('|', array_reverse($this->options)) . ']')
                    ->as($response)
            );
            assert($response instanceof Value);
            $this->response = $response->getValue();
            if (in_array($this->response, $this->options)) {
                return;
            }
        } while (true);
    }
}
