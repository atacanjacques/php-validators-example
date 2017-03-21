<?php

namespace ValidatorsExample\examples;

require __DIR__ . './../../vendor/autoload.php';

class Benchmark
{
    const INPUT_OK = [
        'digitField' => 1,
        'usernameField' => 'long_valid-username',
        'dateField' => '2017-03-05 11:12:13',
        'booleanField' => 'true',
        'optionField' => 'person',
        'optionRequiredField' => 'csv',
        'arrayOfDigits' => [123, 234, 345],
        'arrayOfObjects' => [
            ['optionRequiredField' => 'csv', 'digitField' => 12],
            ['optionRequiredField' => 'xls', 'digitField' => 13]
        ]
    ];

    public static function run()
    {
        $validators = [
            'zend' => new ZendExample(),
            'symfony' => new SymfonyExample(),
            'respect' => new RespectExample(),
            'illuminate' => new IlluminateExample(),
        ];

        $loopCount = 1000;

        $results = [];
        $memResults = [];
        foreach ($validators as $name => $validator) {
            $start = microtime(true);
            $startMem = memory_get_usage();
            for ($i = 0; $i < $loopCount; $i++) {
                $validator->exampleValidation(static::INPUT_OK);
            }
            $results[$name] = microtime(true) - $start;
            $memResults[$name] = memory_get_usage() - $startMem;

        }

        $oneLoopAverageTime = array_map(function ($total) use ($loopCount) {
            return number_format(($total / $loopCount) * 1000, 3);
        }, array_values($results));

        $oneLoopAverageMem = array_map(function ($total) use ($loopCount) {
            return $total / $loopCount;
        }, array_values($memResults));

        echo '"description" ; "loopCount" ; "' . implode('" ; "', array_keys($results)) . '";' . PHP_EOL;

        echo '"average time (sec)" ; ' . $loopCount . ' ; ' . implode(' ; ', array_values($results)) . ';' . PHP_EOL;
        echo '"average mem" ; ' . $loopCount . ' ; ' . implode(' ; ', array_values($memResults)) . ';' . PHP_EOL;

        echo '"average time (ms)" ; 1 ; ' . implode(' ; ', $oneLoopAverageTime) . ';' . PHP_EOL;
        echo '"average mem for" ; 1 ; ' . implode(' ; ', array_values($oneLoopAverageMem)) . ';' . PHP_EOL;
    }
}

/**
 * Sample output for PHP 7.1.2:
 *
    "description" ; "loopCount" ; "zend" ; "symfony" ; "respect" ; "illuminate";
    "average time (sec)" ; 1000 ; 1.1441099643707 ; 2.2912380695343 ; 2.2082159519196 ; 7.563884973526;
    "average mem" ; 1000 ; 517256 ; 4406752 ; 393032 ; 943152;
    "average time (ms)" ; 1 ; 1.144 ; 2.291 ; 2.208 ; 7.564;
    "average mem for" ; 1 ; 517.256 ; 4406.752 ; 393.032 ; 943.152;
 *
 * Illuminate is clear looser in execution time
 * Symfony hogs more memory than others
 *
 */

Benchmark::run();
