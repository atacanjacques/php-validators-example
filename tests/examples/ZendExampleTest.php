<?php

namespace tests\examples;

use ValidatorsExample\examples\ZendExample;

class ZendExampleTest extends BaseTest
{

    public function runValidation(array $input = [])
    {
        return (new ZendExample())->exampleAssertion($input);
    }
}