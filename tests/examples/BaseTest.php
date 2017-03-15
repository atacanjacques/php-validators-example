<?php

namespace tests\examples;


use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    const INPUT_OK = [
        'digitField' => 1,
        'usernameField' => 'long_valid-username',
        'dateField' => '2017-03-05 11:12:13',
        'booleanField' => 'true',
        'optionField' => 'person',
        'optionRequiredField' => 'csv',
        'arrayOfDigits' => [123, 234, 345],
        'arrayOfObjects' => [
            ['optionRequiredField' => 'csv', 'digitField' => 12],
            ['optionRequiredField' => 'xls', 'digitField' => 13]
        ]
    ];
    const INPUT_WITHOUT_OPTIONALS_OK = [
        'dateField' => '2017-03-05 11:12',
        'optionRequiredField' => 'xls'
    ];

    public abstract function runValidation(array $input = []);

    public function testShouldPass()
    {
        $this->assertTrue($this->runValidation(static::INPUT_OK));
    }

    public function testDigitField()
    {
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['digitField' => 1, 'optionRequiredField' => 'xls'])));
        $inputUnset = array_merge(static::INPUT_OK, ['digitField' => null, 'optionRequiredField' => 'xls']);
        unset($inputUnset['digitField']);
        $this->assertTrue($this->runValidation($inputUnset)); // validate optional

        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['digitField' => 'a']))); // not a number
        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['digitField' => -1]))); // not positive
    }

    public function testShouldFailForUsernameField()
    {
        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['usernameField' => 'a']))); // too short
        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['usernameField' => "aa\x01aaaaaaaaaaaaaaaa"]))); // invalid characters
    }

    public function testDateField()
    {
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['dateField' => '2017-03-05 11:12:13'])));
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['dateField' => '2017-03-05 11:12'])));

        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['dateField' => '2017-03-05T11:12:13']))); // not 'YYYY-MM-DD HH:MI:SS' format
    }

    public function testBooleanField()
    {
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['booleanField' => 'true'])));
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['booleanField' => 'false'])));
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['booleanField' => false])));
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['booleanField' => true])));
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['booleanField' => 0])));
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['booleanField' => 1])));
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['booleanField' => '1'])));
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['booleanField' => '0'])));
    }

    public function testOptionField()
    {
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['optionField' => 'person'])));
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['optionField' => 'deal', 'optionRequiredField' => 'xls'])));

        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['optionField' => 'person1']))); // not in ['person', 'deal']
    }

    public function testOptionRequiredField()
    {
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['optionRequiredField' => 'xls'])));
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['optionRequiredField' => 'csv'])));

        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['optionRequiredField' => 'exe']))); // not in ['csv', 'xls']
        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['optionRequiredField' => null]))); // required, is missing
    }

    public function testArrayOfDigits()
    {
        $input = array_merge(static::INPUT_OK, ['arrayOfDigits' => []]);
        $this->assertTrue($this->runValidation($input));
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['arrayOfDigits' => [1]])));

        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['arrayOfDigits' => ['a']]))); // array value not digit
        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['arrayOfDigits' => [-1]]))); // array value not positive
    }

    public function testArrayOfObjects()
    {
        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, [
                'arrayOfObjects' => [
                    ['optionRequiredField' => 'doesnotExists', 'digitField' => 12],
                    ['optionRequiredField' => 'xls', 'digitField' => 13]
                ]
            ]
        ))); // array value not positive
    }

    public function testShouldNotPassBusiness2Rule()
    {
        $optionFieldNotCorrect = array_merge(static::INPUT_OK, ['optionField' => 'deal']);

        $this->assertFalse($this->runValidation($optionFieldNotCorrect));
    }

    public function testShouldNotPassBusiness3Rule()
    {
        $optionFieldNotCorrect = array_merge(static::INPUT_OK, ['digitField' => null]);
        unset($optionFieldNotCorrect['digitField']);

        $this->assertFalse($this->runValidation($optionFieldNotCorrect));
    }

    public function testShouldNotPassBusiness1Rule()
    {
        $optionFieldNotCorrect = array_merge(static::INPUT_OK, ['usernameField' => 'short']);

        $this->assertFalse($this->runValidation($optionFieldNotCorrect));
    }

    public function testShouldValidateAsTrue()
    {
        $this->assertTrue($this->runValidation(static::INPUT_OK));
    }


}