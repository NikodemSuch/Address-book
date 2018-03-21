<?php

namespace Tests\AppBundle\Validator\Constraints;

use AppBundle\Validator\Constraints\ValidPhoneNumbers;
use AppBundle\Validator\Constraints\ValidPhoneNumbersValidator;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;

class ValidPhoneNumberValidatorTest extends AbstractConstraintValidatorTest
{
    protected function createValidator()
    {
        return new ValidPhoneNumbersValidator();
    }

    /**
     * @param string $phoneNumber
     * @dataProvider getValidPhoneNumberDataProvider
     */
    public function testValidPhoneNumber($phoneNumber)
    {
        $constraint = new ValidPhoneNumbers();
        $this->validator->validate($phoneNumber, $constraint);

        $this->assertNoViolation();
    }

    public function getValidPhoneNumberDataProvider()
    {
        return [
            [['1234567']],
            [['12 345 67']],
            [['123 456 7']],
            [['+1234567']],
            [['+(12)34567']],
            [['(+12)34567']],
            [['123(45)67']],
            [['1234(5)67']],
            [['123-45-67']],
        ];
    }

    /**
     * @param string $phoneNumber
     * @dataProvider getInvalidPhoneNumberDataProvider
     */
    public function testInvalidPasswords($phoneNumber)
    {
        $constraint = new ValidPhoneNumbers();
        $this->validator->validate($phoneNumber, $constraint);

        $this->buildViolation('Invalid phone number.')
             ->assertRaised();
    }

    public function getInvalidPhoneNumberDataProvider()
    {
        return [
            [['123456']],
            [['123 456']],
            [['123[[4]567']],
            [['1234_567']],
            [['12+34567']],
            [['123-456']],
            [['1234567-']],
            [['12(+345)67']],
            [['-1234567']],
            [['123)45(67']],
            [['12(34567']],
            [['12345)67']],
            [['123()4567']],
            [['123(-)4567']],
            [['123(4-5)67']],
            [['123(45-)67']],
            [['123(4-5)67']],
            [['123- (45)67']],
            [['123(45- )67']],
            [['123(45) -67']],
            [['123(45)  -67']],
        ];
    }
}
