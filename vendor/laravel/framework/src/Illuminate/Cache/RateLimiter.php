<?php

namespace Illuminate\Cache;

use Illuminate\Contracts\Cache\Repository as Cache;

class RateLimiter
{
    /**
     * The cache store implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Create a new rate limiter instance.
     *
     * @param  \Illuminate\Contracts\Cache\Repository $cache
     * @return void
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Determine if the given key has been "accessed" too many times.
     *
     * @param  string $key
     * @param  int $maxAttempts
     * @param  int $decayMinutes
     * @return bool
     */
    public function tooManyAttempts($key, $maxAttempts, $decayMinutes = 1)
    {
        if ($this->cache->has($key . ':lockout')) {
            return true;
        }

        if ($this->attempts($key) > $maxAttempts) {
            $this->cache->add($key . ':lockout', time() + ($decayMinutes * 60), $decayMinutes);

            return true;
        }

        return false;
    }

    /**
     * Get the number of attempts for the given key.
     *
     * @param  string $key
     * @return mixed
     */
    public function attempts($key)
    {
        return $this->cache->get($key, 0);
    }

    /**
     * Increment the counter for a given key for a given decay time.
     *
     * @param  string $key
     * @param  int $decayMinutes
     * @return int
     */
    public function hit($key, $decayMinutes = 1)
    {
        $this->cache->add($key, 1, $decayMinutes);

        return (int)$this->cache->increment($key);
    }

    /**
     * Clear the hits and lockout for the given key.
     *
     * @param  string $key
     * @return void
     */
    public function clear($key)
    {
        $this->cache->forget($key);

        $this->cache->forget($key . ':lockout');
    }

    /**
     * Get the number of seconds until the "key" is accessible again.
     *
     * @param  string $key
     * @return int
     */
    public function availableIn($key)
    {
        return $this->cache->get($key . ':lockout') - time();
    }
}
