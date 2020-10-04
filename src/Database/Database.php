<?php
declare(strict_types=1);

namespace StephanSchuler\EvalChannelCli\Database;

use PDO;
use StephanSchuler\EvalChannelCli\Environment\Session;

final class Database
{
    private const DATABASE_NAME = 'database.sqlite3';

    /** @var PDO */
    private $pdo;

    public function __construct(string $dataDir)
    {
        $this->setup($dataDir);
    }

    public static function common(Session $environment): self
    {
        $fileName = $environment->getCommonPath(self::DATABASE_NAME);
        return new static($fileName);
    }

    public static function session(Session $environment): self
    {
        $fileName = $environment->getSessionPath(self::DATABASE_NAME);
        return new static($fileName);
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }

    private function setup(string $fileName): void
    {
        $dataDir = dirname($fileName);
        @mkdir($dataDir, 0700, true);

        $this->pdo = new PDO(
            'sqlite:' . $fileName
        );
    }
}
