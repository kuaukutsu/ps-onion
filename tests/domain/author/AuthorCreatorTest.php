<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\domain\author;

use DI\NotFoundException;
use DI\DependencyException;
use PHPUnit\Framework\TestCase;
use kuaukutsu\ps\onion\tests\Container;
use kuaukutsu\ps\onion\domain\entity\author\AuthorPerson;
use kuaukutsu\ps\onion\domain\service\AuthorCreator;

final class AuthorCreatorTest extends TestCase
{
    use Container;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testCreateFromInputSuccess(): void
    {
        $creator = self::get(AuthorCreator::class);
        $author = $creator->createFromInputData(
            new AuthorPerson('test testov')
        );

        self::assertEquals('Test Testov', $author->person->name);
    }
}
