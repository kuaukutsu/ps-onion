<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\cache;

use DateInterval;
use Psr\SimpleCache\CacheInterface;
use kuaukutsu\ps\onion\domain\exception\NotImplementedException;

/**
 * Минимально необходимая реализация для CacheInterface
 */
final class FileCache implements CacheInterface
{
    private string $tmpdir;

    public function __construct(?string $tmpdir = null)
    {
        $this->tmpdir = $tmpdir ?? sys_get_temp_dir();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $data = @file_get_contents($this->makeFilePath($key));
        if ($data === false) {
            return $default;
        }

        $payload = FileData::unserialize($data);
        if ($payload === false) {
            $this->delete($key);
            return $default;
        }

        return $payload;
    }

    public function set(string $key, mixed $value, DateInterval | int | null $ttl = null): bool
    {
        return (bool)file_put_contents(
            $this->makeFilePath($key),
            FileData::serialize($value, $ttl),
            LOCK_EX,
        );
    }

    public function delete(string $key): bool
    {
        return @unlink($this->makeFilePath($key));
    }

    public function clear(): bool
    {
        $filesInDir = glob($this->tmpdir . DIRECTORY_SEPARATOR . '*');
        if ($filesInDir === false) {
            return false;
        }

        $filesProccessed = array_map('unlink', $filesInDir);
        return count($filesProccessed) === count($filesInDir);
    }

    public function has(string $key): bool
    {
        $filepath = $this->makeFilePath($key);
        if (file_exists($filepath) === false) {
            return false;
        }

        $data = @file_get_contents($filepath);
        if ($data === false) {
            return false;
        }

        $expired = FileData::checkTtl($data);
        if ($expired === false) {
            $this->delete($key);
            return false;
        }

        return true;
    }

    /**
     * @throws NotImplementedException
     */
    public function getMultiple(iterable $keys, mixed $default = null): never
    {
        throw new NotImplementedException();
    }

    /**
     * @param iterable<mixed, mixed> $values
     * @throws NotImplementedException
     */
    public function setMultiple(iterable $values, DateInterval | int | null $ttl = null): never
    {
        throw new NotImplementedException();
    }

    /**
     * @throws NotImplementedException
     */
    public function deleteMultiple(iterable $keys): never
    {
        throw new NotImplementedException();
    }

    private function makeFilePath(string $key): string
    {
        return $this->tmpdir . DIRECTORY_SEPARATOR . $this->generateFilename($key);
    }

    private function generateFilename(string $key): string
    {
        /**
         * @note https://php.watch/articles/php-hash-benchmark
         */
        return hash('xxh3', strtolower($key));
    }
}
