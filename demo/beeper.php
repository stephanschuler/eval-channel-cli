#!/usr/bin/env php
<?php
declare(strict_types=1);

use StephanSchuler\EvalChannelCli\ConsoleAdapter;
use StephanSchuler\EvalChannelCli\Messages\Announcement;
use StephanSchuler\EvalChannelCli\Messages\Beep;
use StephanSchuler\EvalChannelCli\Messages\Environment;
use StephanSchuler\EvalChannelCli\Messages\Question;
use StephanSchuler\EvalChannelCli\Values\Value;

require_once __DIR__ . '/../vendor/autoload.php';

new class (trim($argv[1] ?? '')) {
    private $console;

    public function __construct(string $beeps)
    {
        $this->console = ConsoleAdapter::open();

        if ($beeps === '') {
            $this->ask();
            return;
        }
        if ($beeps === '-') {
            $this->fromStdin();
            return;
        }
        $this->beepUntil((int)$beeps);
    }

    public function __destruct()
    {
        $this->console->send(
            Announcement::notify('')
        );
    }

    private function ask(): void
    {
        do {
            $this->console->send(
                Question::askFor('Wie oft?')
                    ->as($anzahl)
            );
            assert($anzahl instanceof Value);
            $beeps = (int)$anzahl->__toString();
        } while ($beeps <= 0);

        $this->beepUntil($beeps);
    }

    private function fromStdin(): void
    {
        $i = 0;
        do {
            $data = fgets(STDIN);
            if ($data === false) {
                return;
            }
            $this->beep($i, false);
            $i++;
            fputs(STDOUT, $data);
        } while (true);
    }

    private function beepUntil(int $beeps): void
    {
        for ($i = 0; $i < $beeps; $i++) {
            $i && sleep(1);
            $this->dot($i);
            $this->beep($i);
        }
    }

    private function dot(int $i): void
    {
        switch ($i % 2) {
            case 0:
                $this->console->send(
                    Announcement::notify('.')
                        ->withoutNewline()
                );
                break;
            case 1:
                $this->console->send(
                    Announcement::warn('.')
                        ->withoutNewline()
                );
                break;
        }
    }

    private function beep(int $i, bool $dot = true): void
    {
        $this->console->send(
            Environment::introduce('beeps')
                ->withValue(Value::instant((string)($i + 1))),
            Beep::ring()
        );
    }
};
