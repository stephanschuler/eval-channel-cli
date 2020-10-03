<?php
declare(strict_types=1);

namespace StephanSchuler\EvalChannelCli;

use StephanSchuler\EvalChannelCli\Stream\Stream;

final class Shell
{
    protected $stdout;
    protected $stderr;
    protected $protocol;

    public function __construct(
        Stream $stdout,
        Stream $stderr,
        Stream $protocol
    ) {
        $this->stdout = $stdout;
        $this->stderr = $stderr;
        $this->protocol = $protocol;
    }

    public function protocol(string $message): self
    {
        $this->protocol->send($message);
        return $this;
    }

    public function stdout(string $message): self
    {
        $this->stdout->send($message);
        return $this;
    }

    public function stderr(string $message): self
    {
        $this->stderr->send($message);
        return $this;
    }
}