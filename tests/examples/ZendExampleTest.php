<?php

namespace tests\examples;

use ValidatorsExample\examples\ZendExample;

class ZendExampleTest extends BaseTest
{

    public function runValidation(array $input = [])
    {
        try {
            return (new ZendExample())->exampleAssertion($input);
        } catch (\RuntimeException $exception) {
            return false;
        }
    }

    // TODO add some tests to check validator error messages
}