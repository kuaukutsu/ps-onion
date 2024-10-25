<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\cache;

use DateInterval;
use DateTimeImmutable;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\cache
 */
final readonly class FileDataStorage
{
    /**
     * Version determines the size of the header.
     */
    private const string VERSION = '01';

    /**
     * Header size.
     * 0,2 byte version
     * 2,16 byte ttl timestamp
     */
    private const int HEADER_SIZE = 16;
    private const int HEADER_VERSION_SIZE = 2;
    private const int HEADER_TTL_SIZE = 14;

    /**
     * @param non-empty-string $filepath
     */
    public function __construct(private string $filepath)
    {
    }

    public function write(mixed $data, DateInterval | int | null $ttl = null): bool
    {
        $expirationTimestamp = 0;
        if ($ttl !== null) {
            $expirationTimestamp = match (true) {
                $ttl instanceof DateInterval => (new DateTimeImmutable())->add($ttl)->getTimestamp(),
                default => time() + $ttl,
            };
        }

        $writeByte = file_put_contents(
            $this->filepath,
            $this->makeHeader($expirationTimestamp) . serialize($data),
            LOCK_EX,
        );

        return $writeByte > 0;
    }

    public function read(): mixed
    {
        $data = @file_get_contents($this->filepath);
        if ($data === false || $this->notExpired($data) === false) {
            return false;
        }

        return unserialize(
            substr($data, self::HEADER_SIZE),
            [
                'allowed_classes' => true,
            ]
        );
    }

    public function exists(): bool
    {
        if (file_exists($this->filepath) === false) {
            return false;
        }

        $header = @file_get_contents($this->filepath, false, null, 0, self::HEADER_SIZE);
        if ($header === false) {
            return false;
        }

        return $this->notExpired($header);
    }

    public function delete(): bool
    {
        if (file_exists($this->filepath) && @unlink($this->filepath)) {
            clearstatcache(true, $this->filepath);
            return true;
        }

        return false;
    }

    private function makeHeader(int $expirationTimestamp): string
    {
        return str_pad(self::VERSION, self::HEADER_VERSION_SIZE)
            . str_pad((string)$expirationTimestamp, self::HEADER_TTL_SIZE);
    }

    private function notExpired(string $data): bool
    {
        $version = substr($data, 0, self::HEADER_VERSION_SIZE);
        if ($version === self::VERSION) {
            $timestamp = (int)trim(substr($data, self::HEADER_VERSION_SIZE, self::HEADER_TTL_SIZE));
            if ($timestamp > 0 && $timestamp < time()) {
                $this->delete();
                return false;
            }
        }

        return true;
    }
}
