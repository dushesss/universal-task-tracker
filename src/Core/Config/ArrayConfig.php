<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Core\Config;

/**
 * ArrayConfig
 *
 * Simple array-based config storage with dot access.
 */
class ArrayConfig implements ConfigInterface
{
    /**
     * @var array<string,mixed>
     */
    private $values;

    /**
     * @param array<string,mixed> $values Initial config values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, $default = null)
    {
        $segments = explode('.', $key);
        $value = $this->values;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->values;
    }
} 