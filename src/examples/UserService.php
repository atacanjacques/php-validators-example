<?php

namespace ValidatorsExample\examples;

class UserService
{
    public function isUserExists($name) {
        return mb_strlen($name) > 6; // to emulate different results
    }

}