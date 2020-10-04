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

$console = ConsoleAdapter::open();
$console->send(
    Question::askFor('Wie oft?')
        ->as($anzahl)
);

assert($anzahl instanceof Value);

for ($i = 0; $i < (int)$anzahl->__toString(); $i++) {
    $i && sleep(1);
    $console->send(
        Announcement::notify('.')
            ->withoutNewline(),
        Environment::introduce('beeps')
            ->withValue(Value::instant((string)$i)),
        Beep::ring()
    );
}

$console->send(
    Announcement::notify('')
);
