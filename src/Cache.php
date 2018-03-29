<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 18-3-29
 */

namespace fk\openweixin;

class Cache
{

    protected $runtimeDirectory = __DIR__ . '/runtime/cache';

    public function __construct($runtime = null)
    {
        if ($runtime) $this->runtimeDirectory = $runtime;
    }

    /**
     * @param string $key
     * @param string $value
     */
    protected function write($key, $value)
    {
        file_put_contents("$this->runtimeDirectory/$key", $value);
    }


    /**
     * @param string $key
     * @param mixed $value
     * @param int $ttl Time To Live, how long this value should live
     */
    public function store($key, $value, $ttl)
    {
        $data = [
            'value' => $value,
            'expires_after' => time() + $ttl
        ];
        $this->write($key, serialize($data));
    }

    public function retrieve($key)
    {
        $filename = "$this->runtimeDirectory/$key";
        if (false === file_exists($filename)) return null;

        $data = file_get_contents($filename);
        if (!$data) return $data;

        $data = unserialize($data);
        if (!is_array($data)) return null;
        ['value' => $value, 'expires_after' => $expiresAfter] = $data;
        if ($expiresAfter > time()) return $value;

        $this->forget($key);
        return null;
    }

    public function forget($key)
    {
        $this->write($key, '');
    }
}