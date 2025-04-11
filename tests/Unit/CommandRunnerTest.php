<?php
namespace Redberry\LaravelPackageInit {

    use RuntimeException;

    if (!function_exists('Redberry\LaravelPackageInit\exec')) {
        /**
         * Fake exec function.
         *
         * If the $command contains the word "fail", simulate a failure.
         */
        function exec($command, &$output, &$return_var)
        {
            if (strpos($command, 'fail') !== false) {
                $output = ['error'];
                $return_var = 1;
            } else {
                $output = ['output message'];
                $return_var = 0;
            }
        }
    }

    if (!function_exists('Redberry\LaravelPackageInit\proc_open')) {
        /**
         * Fake proc_open function.
         *
         * If the $command contains the word "fail", simulate a failure by returning false.
         * Otherwise, return a dummy resource using tmpfile().
         */
        function proc_open($command, array $descriptor_spec, &$pipes)
        {
            if (strpos($command, 'fail') !== false) {
                return false;
            }
            return tmpfile();
        }

        if (!function_exists('Redberry\LaravelPackageInit\proc_close')) {
            /**
             * Fake proc_close function.
             *
             * Instead of closing a process resource, we assume our dummy resource is valid,
             * so we simply close it using fclose() and return 0 as exit code.
             */
            function proc_close($process)
            {
                fclose($process);
                return 0;
            }
        }
    }

    it('runs a command successfully with run()', function () {
        $runner = new CommandRunner();

        $runner->run('echo "Hello, world"');

        expect(true)->toBeTrue();
    });

    it('throws an exception on run() when the command fails', function () {
        $runner = new CommandRunner();

        expect(fn() => $runner->run('fail command'))
            ->toThrow(RuntimeException::class, 'Command failed with return code 1: error');
    });

    it('runs an interactive command successfully with runInteractive()', function () {
        $runner = new CommandRunner();

        $runner->runInteractive('echo "Interactive Hello"');

        expect(true)->toBeTrue();
    });

    it('throws an exception on runInteractive() when the command fails', function () {
        $runner = new CommandRunner();

        expect(fn() => $runner->runInteractive('fail interactive command'))
            ->toThrow(RuntimeException::class, 'Failed to execute interactive command: fail interactive command');
    });
}

