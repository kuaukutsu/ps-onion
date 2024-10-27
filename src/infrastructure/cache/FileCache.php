<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\cache;

use Override;
use DateInterval;
use Psr\SimpleCache\CacheInterface;
use kuaukutsu\ps\onion\domain\exception\NotImplementedException;

/**
 * Минимально необходимая реализация для CacheInterface
 */
final readonly class FileCache implements CacheInterface
{
    private string $tmpdir;

    public function __construct(?string $tmpdir = null)
    {
        $this->tmpdir = $tmpdir ?? sys_get_temp_dir();
    }

    #[Override]
    public function get(string $key, mixed $default = null): mixed
    {
        assert($key !== '', 'non-empty-string');

        return (new FileDataStorage($this->makeFilePath($key)))
            ->read() ?: $default;
    }

    #[Override]
    public function set(string $key, mixed $value, DateInterval | int | null $ttl = null): bool
    {
        assert($key !== '', 'non-empty-string');

        return (new FileDataStorage($this->makeFilePath($key)))
            ->write($value, $ttl);
    }

    #[Override]
    public function delete(string $key): bool
    {
        assert($key !== '', 'non-empty-string');

        return (new FileDataStorage($this->makeFilePath($key)))
            ->delete();
    }

    #[Override]
    public function clear(): bool
    {
        /** @var non-empty-string[]|false $filesInDir */
        $filesInDir = glob($this->tmpdir . DIRECTORY_SEPARATOR . '*');
        if ($filesInDir === false) {
            return false;
        }

        $filesProccessed = array_map(
            static fn(string $filePath) => (new FileDataStorage($filePath))->delete(),
            $filesInDir,
        );

        return count($filesProccessed) === count($filesInDir);
    }

    #[Override]
    public function has(string $key): bool
    {
        assert($key !== '', 'non-empty-string');

        return (new FileDataStorage($this->makeFilePath($key)))
            ->exists();
    }

    /**
     * @throws NotImplementedException
     */
    #[Override]
    public function getMultiple(iterable $keys, mixed $default = null): never
    {
        throw new NotImplementedException();
    }

    /**
     * @param iterable<mixed, mixed> $values
     * @throws NotImplementedException
     */
    #[Override]
    public function setMultiple(iterable $values, DateInterval | int | null $ttl = null): never
    {
        throw new NotImplementedException();
    }

    /**
     * @throws NotImplementedException
     */
    #[Override]
    public function deleteMultiple(iterable $keys): never
    {
        throw new NotImplementedException();
    }

    /**
     * @param non-empty-string $key
     * @return non-empty-string
     */
    private function makeFilePath(string $key): string
    {
        /**
         * @note https://php.watch/articles/php-hash-benchmark
         */
        return $this->tmpdir
            . DIRECTORY_SEPARATOR
            . hash('xxh3', strtolower($key));
    }
}
