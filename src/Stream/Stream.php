<?php
declare(strict_types=1);

namespace StephanSchuler\EvalChannelCli\Stream;

final class Stream
{
    private $resource;
    private $transformation;

    private function __construct($resource, callable ...$transformation)
    {
        $this->resource = $resource;
        if (!is_resource($resource)) {
            throw new \Exception('No resource provided!');
        }
        $this->transformation = $transformation;
    }

    public static function fromStream(string $stream): self
    {
        $stream = fopen($stream, 'w');
        return new static($stream);
    }

    public function __destruct()
    {
//        fclose($this->resource);
    }

    public function send(string $message): void
    {
        foreach (array_reverse($this->transformation) as $transformationStep) {
            $message = $transformationStep($message);
        }
        fputs($this->resource, $message);
    }

    public function transform(callable $transformation): self
    {
        return new static($this->resource, $transformation, ... $this->transformation);
    }
}
