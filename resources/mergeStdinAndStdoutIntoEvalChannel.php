#!/usr/bin/env php
<?php
declare(strict_types=1);

$arguments = array_slice($argv, 1);
$arguments = array_map('escapeshellarg', $arguments);
$arguments = join(' ', $arguments);

$descriptorSpec = [
    0 => STDIN,
    1 => ["pipe", "w"],
    2 => ["pipe", "w"],
    3 => ["pipe", "w"],
];

$fp = proc_open($arguments, $descriptorSpec, $pipes);
if (!is_resource($fp)) {
    die('xxx');
}

$openStreams = [
    'stdout' => $pipes[1],
    'stderr' => $pipes[2],
    'data' => $pipes[3]
];
foreach ($openStreams as $stream) {
    stream_set_blocking($stream, false);
}

do {
    $read = $openStreams;
    $write = null;
    $except = null;

    stream_select($read, $write, $except, 0);

    foreach ($read as $name => $stream) {
        switch ($name) {
            case 'stdout':
            case 'stderr':
                $data = stream_get_contents($stream);
                break;
            case 'data':
                $data = fgets($stream);
                break;
        }

        if ($data === false) {
            unset($openStreams[$name]);
            continue;
        }

        switch ($name) {
            case 'stdout':
                $command = '>&1 echo -n ' . escapeshellarg($data);
                $command = base64_encode($command) . PHP_EOL;
                break;
            case 'stderr':
                $command = '>&2 echo -n ' . escapeshellarg($data);
                $command = base64_encode($command) . PHP_EOL;
                break;
            case 'data':
                $command = $data;
                break;
        }

        fputs(STDOUT, $command);
    }

} while ($fp && $openStreams);
