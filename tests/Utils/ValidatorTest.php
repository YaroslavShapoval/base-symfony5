<?php

// partially copied from symfony/demo

namespace App\Tests\Utils;

use App\Utils\Validator;
use Generator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    /**
     * @dataProvider validUsernamesDataProvider
     * @param $username
     */
    public function testValidateUsername($username): void
    {
        $this->assertSame($username, $this->validator->validateUsername($username));
    }

    public function validUsernamesDataProvider(): ?Generator
    {
        yield ['user'];
        yield ['username'];
        yield ['user_name'];
        yield ['user.name'];
        yield [str_repeat('a', 255)];
    }

    public function testValidateUsernameEmpty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The username can not be empty.');
        $this->validator->validateUsername(null);
    }

    public function testValidateUsernameInvalid(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The username must contain only lowercase latin characters, underscores and dots.');
        $this->validator->validateUsername('INVALID');
    }

    public function testValidateUsernameTooShort(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The username must be at least 4 characters long.');
        $this->validator->validateUsername('aaa');
    }

    public function testValidateUsernameTooLong(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The username must be at most 255 characters long.');
        $this->validator->validateUsername(str_repeat('a', 256));
    }

    public function testValidatePassword(): void
    {
        $test = 'password';

        $this->assertSame($test, $this->validator->validatePassword($test));
    }

    public function testValidatePasswordEmpty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The password can not be empty.');
        $this->validator->validatePassword(null);
    }

    public function testValidatePasswordInvalid(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The password must be at least 6 characters long.');
        $this->validator->validatePassword('12345');
    }

    public function testValidateEmail(): void
    {
        $test = 'test@example.com';

        $this->assertSame($test, $this->validator->validateEmail($test));
    }

    public function testValidateEmailEmpty(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The email can not be empty.');
        $this->validator->validateEmail(null);
    }

    /**
     * @dataProvider invalidEmailsDataProvider
     * @param $email
     */
    public function testValidateEmailInvalid($email): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('The email address is not valid.');
        $this->validator->validateEmail($email);
    }

    public function invalidEmailsDataProvider(): ?Generator
    {
        yield ['invalid'];
        yield ['invalid@'];
        yield ['@invalid'];
        yield ['@invalid@'];
        yield ['@@'];
    }
}