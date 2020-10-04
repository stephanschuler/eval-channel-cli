<?php
declare(strict_types=1);

namespace StephanSchuler\EvalChannelCli\Messages;

use StephanSchuler\EvalChannelCli\ConsoleAdapter;
use StephanSchuler\EvalChannelCli\Shell;

final class Beep implements Message
{
    private const BELL_CHR = 7;

    public static function ring(): self
    {
        return new static();
    }

    public function execute(Shell $shell, ConsoleAdapter $console): void
    {
        $shell->protocol(
            sprintf('>/dev/tty echo -n %s', escapeshellarg(chr(self::BELL_CHR)))
        );
    }
}
