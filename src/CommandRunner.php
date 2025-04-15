<?php

namespace Redberry\LaravelPackager;

class CommandRunner
{
    public function run($command): void
    {
        $output = [];
        $returnVar = 0;

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \RuntimeException("Command failed with return code {$returnVar}: ".implode("\n", $output));
        }
    }

    public function runInteractive(string $command): void
    {
        // Use proc_open to ensure the command uses the current STDIN, STDOUT, and STDERR.
        $descriptorSpec = [
            0 => ['file', 'php://stdin', 'r'],
            1 => ['file', 'php://stdout', 'w'],
            2 => ['file', 'php://stderr', 'w'],
        ];

        $process = proc_open($command, $descriptorSpec, $pipes);

        if (is_resource($process)) {
            proc_close($process);
        } else {
            throw new \RuntimeException("Failed to execute interactive command: {$command}");
        }
    }
}
