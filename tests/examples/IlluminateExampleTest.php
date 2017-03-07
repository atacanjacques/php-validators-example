<?php

namespace tests\examples;

use ValidatorsExample\examples\IlluminateExample;

class IlluminateExampleTest extends BaseTest
{
    public function runValidation(array $input = [])
    {
        try {
            return (new IlluminateExample())->exampleAssertion($input);
        } catch (\RuntimeException $exception) {
            return false;
        }
    }

    // TODO add some tests to check validator error messages
}