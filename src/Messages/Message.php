<?php
declare(strict_types=1);

namespace StephanSchuler\EvalChannelCli\Messages;

use StephanSchuler\EvalChannelCli\ConsoleAdapter;
use StephanSchuler\EvalChannelCli\Stream\Streams;

interface Message
{
    public function execute(Streams $streams, ConsoleAdapter $console): void;
}

