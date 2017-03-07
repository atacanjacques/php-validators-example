<?php

namespace tests\examples;


use ValidatorsExample\examples\SymfonyExample;

class SymfonyExampleTest extends BaseTest
{
    public function runValidation(array $input = [])
    {
        return (new SymfonyExample())->exampleAssertion($input);
    }
}