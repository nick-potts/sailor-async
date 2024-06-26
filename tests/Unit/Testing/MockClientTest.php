<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Testing;

use Spawnia\Sailor\Response;
use Spawnia\Sailor\Testing\MockClient;
use Spawnia\Sailor\Tests\TestCase;

final class MockClientTest extends TestCase
{
    public function testCallsMock(): void
    {
        $query = 'simple';
        $variables = new \stdClass();

        $response = new Response();

        $responseMock = self::createPartialMock(Invokable::class, ['__invoke']);
        $responseMock->expects(self::exactly(2))
            ->method('__invoke')
            ->with($query, $variables)
            ->willReturn($response);

        $mockClient = new MockClient($responseMock);

        self::assertSame($response, $mockClient->request($query, $variables));

        $storedRequest = $mockClient->storedRequests[0];

        self::assertSame($query, $storedRequest->query);
        self::assertSame($variables, $storedRequest->variables);

        $mockClient->requestAsync($query, $variables)->wait();

        $storedRequest = $mockClient->storedRequests[1];

        self::assertSame($query, $storedRequest->query);
        self::assertSame($variables, $storedRequest->variables);
    }
}
