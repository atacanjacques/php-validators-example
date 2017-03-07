<?php

namespace tests\examples;


use ValidatorsExample\examples\SymfonyExample;

class SymfonyExampleTest extends BaseTest
{
    public function runValidation(array $input = [])
    {
        try {
            return (new SymfonyExample())->exampleAssertion($input);
        } catch (\RuntimeException $exception) {
            return false;
        }
    }

    // TODO add some tests to check validator error messages
}