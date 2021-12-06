<?php

namespace code\storage\filesystem;

class DirectoryAttributes  extends StorageArrayAccess implements StorageAttributes {

    /**
     * @var string
     */
    private $type = StorageAttributes::TYPE_DIRECTORY;

    /**
     * @var string
     */
    private $path;

    /**
     * @var int|null
     */
    private $lastModified;

    /**
     * @var array
     */
    private $extraMetadata;

    public function __construct(string $path, ?int $lastModified = null, array $extraMetadata = []) {
        $this->path = $path;
        $this->lastModified = $lastModified;
        $this->extraMetadata = $extraMetadata;
    }

    public function path(): string {
        return $this->path;
    }

    public function type(): string {
        return StorageAttributes::TYPE_DIRECTORY;
    }

    public function lastModified(): ?int {
        return $this->lastModified;
    }

    public function extraMetadata(): array {
        return $this->extraMetadata;
    }

    public function isFile(): bool {
        return false;
    }

    public function isDir(): bool {
        return true;
    }

    public function withPath(string $path): StorageAttributes {
        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    public static function fromArray(array $attributes): StorageAttributes {
        return new DirectoryAttributes(
                $attributes[StorageAttributes::ATTRIBUTE_PATH],
                $attributes[StorageAttributes::ATTRIBUTE_LAST_MODIFIED] ?? null,
                $attributes[StorageAttributes::ATTRIBUTE_EXTRA_METADATA] ?? []
        );
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array {
        return [
            StorageAttributes::ATTRIBUTE_TYPE => $this->type,
            StorageAttributes::ATTRIBUTE_PATH => $this->path,
            StorageAttributes::ATTRIBUTE_LAST_MODIFIED => $this->lastModified,
            StorageAttributes::ATTRIBUTE_EXTRA_METADATA => $this->extraMetadata,
        ];
    }

}
