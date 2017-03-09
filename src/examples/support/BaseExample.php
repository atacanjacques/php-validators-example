<?php

namespace ValidatorsExample\examples\support;

abstract class BaseExample
{
    protected $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public abstract function exampleAssertion(array $input = []);

}