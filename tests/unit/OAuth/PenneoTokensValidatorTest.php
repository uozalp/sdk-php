<?php

namespace Penneo\SDK\Tests\Unit\OAuth;

use Carbon\Carbon;
use Penneo\SDK\OAuth\Tokens\PenneoTokenValidator;
use Penneo\SDK\OAuth\Tokens\PenneoTokens;
use PHPUnit\Framework\TestCase;

class PenneoTokensValidatorTest extends TestCase
{
    public function testValidatesThePenneoTokensWhenBothAreExpired(): void
    {
        $tokens = new PenneoTokens(
            'access_token',
            'refresh_token',
            Carbon::yesterday()->getTimestamp(),
            Carbon::yesterday()->getTimestamp()
        );

        $this->assertFalse(PenneoTokenValidator::isValid($tokens));
    }

    public function testReturnsTrueWhenBothTokensAreValid(): void
    {
        $tokens = new PenneoTokens(
            'access_token',
            'refresh_token',
            Carbon::tomorrow()->getTimestamp(),
            Carbon::tomorrow()->getTimestamp()
        );

        $this->assertTrue(PenneoTokenValidator::isValid($tokens));
    }

    public function testReturnsTrueWhenAccessTokenIsExpiredAndRefreshTokenIsValid(): void
    {
        $tokens = new PenneoTokens(
            'access_token',
            'refresh_token',
            Carbon::yesterday()->getTimestamp(),
            Carbon::tomorrow()->getTimestamp()
        );

        $this->assertTrue(PenneoTokenValidator::isValid($tokens));
    }

    public function testReturnsTrueWhenAccessTokenIsValidAndRefreshTokenIsExpired(): void
    {
        $tokens = new PenneoTokens(
            'access_token',
            'refresh_token',
            Carbon::yesterday()->getTimestamp(),
            Carbon::tomorrow()->getTimestamp()
        );

        $this->assertTrue(PenneoTokenValidator::isValid($tokens));
    }

    /**
     * @testWith [5, "seconds"]
     *           [0, "seconds"]
     *           [-1, "day"]
     *           [-1, "year"]
     */
    public function testReturnsFalseWhenBothTokensAreExpired(int $timeDiffValue, string $timeDiffUnit)
    {
        $expiredTs = Carbon::now()->addUnit($timeDiffUnit, $timeDiffValue)->getTimestamp();
        $tokens = new PenneoTokens('access_token', 'refresh_token', $expiredTs, $expiredTs);

        $this->assertFalse(PenneoTokenValidator::isValid($tokens));
    }
}
