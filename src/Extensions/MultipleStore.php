<?php

namespace Calvin\Extensions;

use Illuminate\Cache\CacheManager;
use Illuminate\Cache\DatabaseStore;
use Illuminate\Cache\RedisStore;
use Illuminate\Contracts\Cache\Store;

class MultipleStore implements Store
{
    public $temporaryCacheManager;
    public $persistentCacheManager;

    public function __construct($app){
        $this->initTemporaryCacheManager($app);
        $this->initPersistentCacheManager($app);
    }
    private function initTemporaryCacheManager($app)
    {
        $config = $app['config']["cache.stores.redis"];
        $cacheManager = new CacheManager($app);
        $redis = $app['redis'];
        $connection = $config['connection'] ?? 'default';
        $prefix = $config['prefix'] ?? $app['config']['cache.prefix'];
        $this->temporaryCacheManager = $cacheManager->repository(new RedisStore($redis, $prefix, $connection));
    }

    private function initPersistentCacheManager($app)
    {
        $config = $app['config']["cache.stores.database"];
        $cacheManager2 = new CacheManager($app);
        $connection = $app['db']->connection($config['connection'] ?? null);
        $prefix = null;
        $this->persistentCacheManager = $cacheManager2->repository(
            new DatabaseStore(
                $connection, $config['table'], $prefix
            )
        );
    }

    public function get($key) {
        $value = $this->temporaryCacheManager->get($key);
        if(!$value){
            $value = $this->persistentCacheManager->get($key);

            if($value)
            {
                $this->temporaryCacheManager->put($key, $value);
            }
        }
        return $value;
    }
    public function many(array $keys) {
        // TODO: not sure that
    }
    public function put($key, $value, $seconds) {
        $this->temporaryCacheManager->put($key, $value, $seconds);
        return $this->persistentCacheManager->put($key, $value, $seconds);
    }
    public function putMany(array $values, $seconds) {
        $this->temporaryCacheManager->put($values, $seconds);
        return $this->persistentCacheManager->put($values, $seconds);
    }
    public function increment($key, $value = 1) {
        $this->temporaryCacheManager->increment($key, $value);
        return $this->persistentCacheManager->increment($key, $value);
    }
    public function decrement($key, $value = 1) {
        $this->temporaryCacheManager->decrement($key, $value);
        return $this->persistentCacheManager->decrement($key, $value);
    }
    public function forever($key, $value) {
        $this->temporaryCacheManager->forever($key, $value);
        return $this->persistentCacheManager->forever($key, $value);
    }
    public function forget($key) {
        $this->temporaryCacheManager->forget($key);
        return $this->persistentCacheManager->forget($key);
    }
    public function flush() {
        $this->temporaryCacheManager->flush();
        return $this->persistentCacheManager->flush();
    }
    public function getPrefix() {
        $this->temporaryCacheManager->getPrefix();
        return $this->persistentCacheManager->getPrefix();
    }
}
