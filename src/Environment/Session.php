<?php
declare(strict_types=1);

namespace StephanSchuler\EvalChannelCli\Environment;

final class Session
{
    private const FOL = '.evalChannelCli';

    protected $home;
    protected $shellPid;

    public function __construct(string $home, int $shellPid)
    {
        $this->home = $home;
        $this->shellPid = $shellPid;
    }

    public static function current(): self
    {
        $home = rtrim(realpath((getenv('HOME') ?: '/tmp')), DIRECTORY_SEPARATOR);
        $shellPid = (int)getenv('SHELL_PID');
        return new static(
            $home,
            $shellPid
        );
    }

    public function getSessionPath(string $path = ''): string
    {
        return self::joinPaths(
            $this->home,
            self::FOL,
            'session',
            (string)$this->shellPid,
            $path
        );
    }

    public function getCommonPath(string $path = ''): string
    {
        return self::joinPaths(
            $this->home,
            self::FOL,
            'common',
            $path
        );
    }

    private static function joinPaths(string ...$paths): string
    {
        $paths = array_map(function (string $segment): string {
            return trim($segment, DIRECTORY_SEPARATOR);
        }, $paths);
        $paths = array_filter($paths);
        return DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, $paths);
    }
}
