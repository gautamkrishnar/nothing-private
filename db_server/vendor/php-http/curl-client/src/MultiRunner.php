<?php

declare(strict_types=1);

namespace Http\Client\Curl;

use Http\Client\Exception\RequestException;

/**
 * Simultaneous requests runner.
 *
 * @license http://opensource.org/licenses/MIT MIT
 * @author  Михаил Красильников <m.krasilnikov@yandex.ru>
 */
class MultiRunner
{
    /**
     * cURL multi handle.
     *
     * @var resource|null
     */
    private $multiHandle;

    /**
     * Awaiting cores.
     *
     * @var PromiseCore[]
     */
    private $cores = [];

    /**
     * Release resources if still active.
     */
    public function __destruct()
    {
        if (is_resource($this->multiHandle)) {
            curl_multi_close($this->multiHandle);
        }
    }

    /**
     * Add promise to runner.
     *
     * @param PromiseCore $core
     */
    public function add(PromiseCore $core): void
    {
        foreach ($this->cores as $existed) {
            if ($existed === $core) {
                return;
            }
        }

        $this->cores[] = $core;

        if (null === $this->multiHandle) {
            $this->multiHandle = curl_multi_init();
        }
        curl_multi_add_handle($this->multiHandle, $core->getHandle());
    }

    /**
     * Remove promise from runner.
     *
     * @param PromiseCore $core
     */
    public function remove(PromiseCore $core): void
    {
        foreach ($this->cores as $index => $existed) {
            if ($existed === $core) {
                curl_multi_remove_handle($this->multiHandle, $core->getHandle());
                unset($this->cores[$index]);

                return;
            }
        }
    }

    /**
     * Wait for request(s) to be completed.
     *
     * @param PromiseCore|null $targetCore
     */
    public function wait(PromiseCore $targetCore = null): void
    {
        do {
            $status = curl_multi_exec($this->multiHandle, $active);
            $info = curl_multi_info_read($this->multiHandle);
            if (false !== $info) {
                $core = $this->findCoreByHandle($info['handle']);

                if (null === $core) {
                    // We have no promise for this handle. Drop it.
                    curl_multi_remove_handle($this->multiHandle, $info['handle']);
                    continue;
                }

                if (CURLE_OK === $info['result']) {
                    $core->fulfill();
                } else {
                    $error = curl_error($core->getHandle());
                    $core->reject(new RequestException($error, $core->getRequest()));
                }
                $this->remove($core);

                // This is a promise we are waited for. So exiting wait().
                if ($core === $targetCore) {
                    return;
                }
            }
        } while ($status === CURLM_CALL_MULTI_PERFORM || $active);
    }

    /**
     * Find core by handle.
     *
     * @param resource $handle
     *
     * @return PromiseCore|null
     */
    private function findCoreByHandle($handle): ?PromiseCore
    {
        foreach ($this->cores as $core) {
            if ($core->getHandle() === $handle) {
                return $core;
            }
        }

        return null;
    }
}
