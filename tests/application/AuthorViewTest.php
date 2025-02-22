<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\application;

use LogicException;
use DI\DependencyException;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use kuaukutsu\ps\onion\application\case\AuthorIndex;
use kuaukutsu\ps\onion\domain\exception\NotFoundException as NotFoundExceptionDomain;
use kuaukutsu\ps\onion\domain\service\AuthorUuidGenerator;

final class AuthorViewTest extends TestCase
{
    use AuthorSetUp;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testAuthorViewSuccess(): void
    {
        $app = self::get(AuthorIndex::class);
        $uuid = self::get(AuthorUuidGenerator::class);
        $domain = $app->get(
            $uuid->generate()->value,
        );

        self::assertEquals('Tester', $domain->name);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testAuthorViewValidateValueError(): void
    {
        $this->expectException(LogicException::class);

        $app = self::get(AuthorIndex::class);
        $app->get('000000-0000-0000-0000');
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testAuthorViewNotFoundError(): void
    {
        $this->expectException(NotFoundExceptionDomain::class);
        $this->expectExceptionMessage("[30363638-6338-8863-b831-333265346631] Author not found.");

        $app = self::get(AuthorIndex::class);
        $app->get('30363638-6338-8863-b831-333265346631');
    }
}
