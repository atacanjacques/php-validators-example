<?php

namespace tests\examples;

use ValidatorsExample\examples\IlluminateExample;

class IlluminateExampleTest extends BaseTest
{
    public function runValidation(array $input = [])
    {
        return (new IlluminateExample())->exampleAssertion($input);
    }
}