<?php

declare(strict_types=1);

namespace Http\Client\Common\Plugin;

use Http\Client\Common\Plugin;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @internal
 */
abstract class SeekableBodyPlugin implements Plugin
{
    protected $useFileBuffer;

    protected $memoryBufferSize;

    /**
     * @param array $config {
     *
     *    @var bool $use_file_buffer    Whether this plugin should use a file as a buffer if the stream is too big, defaults to true
     *    @var int  $memory_buffer_size Max memory size in bytes to use for the buffer before it use a file, defaults to 2097152 (2 mb)
     * }
     */
    public function __construct(array $config = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'use_file_buffer' => true,
            'memory_buffer_size' => 2097152,
        ]);
        $resolver->setAllowedTypes('use_file_buffer', 'bool');
        $resolver->setAllowedTypes('memory_buffer_size', 'int');

        $options = $resolver->resolve($config);

        $this->useFileBuffer = $options['use_file_buffer'];
        $this->memoryBufferSize = $options['memory_buffer_size'];
    }
}
