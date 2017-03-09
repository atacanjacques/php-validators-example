<?php

namespace ValidatorsExample\examples\support;

class UserService
{
    public function isUserExists($name) {
        return mb_strlen($name) > 6; // to emulate different results
    }

}