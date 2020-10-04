#!/usr/bin/env php
<?php
declare(strict_types=1);

use StephanSchuler\EvalChannelCli\ConsoleAdapter;
use StephanSchuler\EvalChannelCli\Messages;
use StephanSchuler\EvalChannelCli\Values\Value;

require_once __DIR__ . '/../vendor/autoload.php';

new class {
    private const SUMME = 'summe';
    private const A = 'summand_a';
    private const B = 'summand_b';

    private $console;

    public function __construct()
    {
        $this->console = ConsoleAdapter::open();

        $summeBefore = $this->fetchSumme();
        $a = $this->fetchA();
        $b = $this->fetchB();

        $summe = $this->calculate($summeBefore, $a, $b);
        $summe->__toString();
        $this->announce($summe);

        $this->console->send(
            Messages\Terminate::successfully()
        );
    }

    private function fetchSumme(): Value
    {
        $summe = Value::instant(
            (string)getenv(self::SUMME)
        );
        $this->console->send(
            Messages\Announcement::notify('Die aktuelle Summe: %s')
                ->withArgument($summe),
            Messages\Announcement::notify('Das wird der neuen Summe hinzugefügt'),
            Messages\Announcement::notify('Wurde aus der Umgebungsvariable "%s" bezogen')
                ->withArgument(Value::instant(self::SUMME))
        );
        return $summe;
    }

    private function fetchA(): Value
    {
        $this->console->send(
            Messages\Question::askFor('Erster Summand?')
                ->as($summand),
            Messages\Announcement::notify('Erster Summand: %s')
                ->withArgument($summand),
            Messages\Environment::introduce(self::A)
                ->withValue($summand),
            Messages\Announcement::notify('Wurde als Umgebungsvariable "%s" gesetzt')
                ->withArgument(Value::instant(self::A))
        );
        return $summand;
    }

    private function fetchB(): Value
    {
        $this->console->send(
            Messages\Question::askFor('Zweiter Summand?')
                ->as($summand),
            Messages\Announcement::notify('Zweiter Summand: %s')
                ->withArgument($summand),
            Messages\Environment::introduce(self::B)
                ->withValue($summand),
            Messages\Announcement::notify('Wurde als Umgebungsvariable "%s" gesetzt')
                ->withArgument(Value::instant(self::B))
        );
        return $summand;
    }

    private function calculate(Value ...$values): Value
    {
        return Value::lazy(function () use ($values): string {
            $values = array_map(function (Value $value): string {
                return $value->__toString();
            }, $values);
            $values = array_map(function (string $value): int {
                return (int)$value;
            }, $values);
            $sum = array_sum($values);
            return (string)$sum;
        });
    }

    private function announce(Value $summe): void
    {
        $this->console->send(
            Messages\Announcement::notify('Die aktuelle Summe ist: %s.')
                ->withArgument($summe),
            Messages\Environment::introduce(self::SUMME)
                ->withValue($summe),
            Messages\Announcement::notify('Wurde als Umgebungsvariable "%s" gesetzt')
                ->withArgument(Value::instant(self::SUMME))
        );
    }
};
