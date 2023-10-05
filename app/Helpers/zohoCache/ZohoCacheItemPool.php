<?php

namespace App\Helpers\zohoCache;

use InvalidArgumentException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class ZohoCacheItemPool implements CacheItemPoolInterface
{
    /**
     * @var CacheItemInterface[]
     */
    private $items;

    /**
     * @var CacheItemInterface[]
     */
    private $deferredItems;

    /**
     * {@inheritdoc}
     *
     * @return CacheItemInterface The corresponding Cache Item.
     */
    public function getItem($key): CacheItemInterface
    {
        return current($this->getItems([$key]));  // @phpstan-ignore-line
    }

    /**
     * {@inheritdoc}
     *
     * @return iterable<CacheItemInterface>
     *   A traversable collection of Cache Items keyed by the cache keys of
     *   each item. A Cache item will be returned for each key, even if that
     *   key is not found. However, if no keys are specified then an empty
     *   traversable MUST be returned instead.
     */
    public function getItems(array $keys = []): iterable
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->hasItem($key) ? clone $this->items[$key] : new ZohoCacheItem($key);
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     *   True if item exists in the cache, false otherwise.
     */
    public function hasItem($key): bool
    {
        $this->isValidKey($key);

        return isset($this->items[$key]) && $this->items[$key]->isHit();
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     *   True if the pool was successfully cleared. False if there was an error.
     */
    public function clear(): bool
    {
        $this->items = [];
        $this->deferredItems = [];

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     *   True if the item was successfully removed. False if there was an error.
     */
    public function deleteItem($key): bool
    {
        return $this->deleteItems([$key]);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     *   True if the items were successfully removed. False if there was an error.
     */
    public function deleteItems(array $keys): bool
    {
        array_walk($keys, [$this, 'isValidKey']);

        foreach ($keys as $key) {
            unset($this->items[$key]);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     *   True if the item was successfully persisted. False if there was an error.
     */
    public function save(CacheItemInterface $item): bool
    {
        $this->items[$item->getKey()] = $item;

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     *   False if the item could not be queued or if a commit was attempted and failed. True otherwise.
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferredItems[$item->getKey()] = $item;

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     *   True if all not-yet-saved items were successfully saved or there were none. False otherwise.
     */
    public function commit(): bool
    {
        foreach ($this->deferredItems as $item) {
            $this->save($item);
        }

        $this->deferredItems = [];

        return true;
    }

    /**
     * Determines if the provided key is valid.
     *
     * @param string $key
     * @return void
     * @throws InvalidArgumentException
     */
    private function isValidKey($key): void
    {
        $invalidCharacters = '{}()/\\\\@:';

        if (!is_string($key) || preg_match("#[$invalidCharacters]#", $key)) {
            throw new InvalidArgumentException('The provided key is not valid: ' . var_export($key, true));
        }
    }
}
