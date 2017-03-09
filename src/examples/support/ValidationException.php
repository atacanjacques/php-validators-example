<?php

namespace ValidatorsExample\examples\support;

class ValidationException extends \InvalidArgumentException
{
    private $messages;

    public function __construct($messages)
    {
        $this->messages = $messages;
    }

    public function getMessages()
    {
        return $this->messages;
    }
}