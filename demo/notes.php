#!/usr/bin/env php
<?php
declare(strict_types=1);

use StephanSchuler\EvalChannelCli\ConsoleAdapter;
use StephanSchuler\EvalChannelCli\Database\Database;
use StephanSchuler\EvalChannelCli\Environment\Session;
use StephanSchuler\EvalChannelCli\Messages\Announcement;
use StephanSchuler\EvalChannelCli\Messages\Poll;
use StephanSchuler\EvalChannelCli\Messages\Question;
use StephanSchuler\EvalChannelCli\Values\Value;

require_once __DIR__ . '/../vendor/autoload.php';


new class {
    private $console;

    private $session;
    private $common;

    public function __construct()
    {
        $this->console = ConsoleAdapter::open();

        $session = Session::current();

        $this->session = $this->setup(
            Database::session($session)
        );
        $this->common = $this->setup(
            Database::common($session)
        );

        $this->loop();
    }

    private function setup(Database $database): Database
    {
        $database->pdo()->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS notes (
	date text NOT NULL,
   	subject text NOT NULL
)
SQL
        );
        return $database;
    }

    private function loop(): void
    {
        static $global = 'global';
        static $lokal = 'lokal';
        static $liste = 'liste';

        do {
            $this->console->send(
                Poll::askFor('Wo sollen die Daten gespeichert werden?')
                    ->withOption($global)
                    ->withOption($lokal)
                    ->withOption($liste)
                    ->as($scope)
            );
            assert($scope instanceof Value);
            if ($scope->getValue() === $liste) {
                $this->list($global, $this->common);
                $this->list($lokal, $this->session);
                continue;
            }
            $this->console->send(
                Question::askFor('Wie lautet der Betreff?')
                    ->as($subject)
            );

            switch ($scope->getValue()) {
                case $global:
                    $this->storeTo($this->common, $subject);
                    break;
                case $lokal:
                    $this->storeTo($this->session, $subject);
                    break;
            }
        } while (true);
    }

    private function storeTo(Database $database, Value $value): void
    {
        $insert = <<<'SQL'
INSERT INTO notes
(date, subject)
VALUES (:date, :subject)
SQL;
        $database
            ->pdo()
            ->prepare($insert)
            ->execute([
                'date' => (new DateTimeImmutable())->format('r'),
                'subject' => $value->getValue(),
            ]);
    }

    private function list(string $source, Database $database): void
    {
        $results = $database
            ->pdo()
            ->query('SELECT * FROM notes')
            ->fetchAll();
        $source = Value::instant($source);
        foreach ($results as $result) {
            $this
                ->console
                ->send(
                    Announcement::notify('%s | %s | %s')
                        ->withArgument($source)
                        ->withArgument(Value::instant($result['date']))
                        ->withArgument(Value::instant($result['subject']))
                );
        }
    }
};
