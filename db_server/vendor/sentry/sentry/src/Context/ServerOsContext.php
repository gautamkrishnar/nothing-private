<?php

declare(strict_types=1);

namespace Sentry\Context;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class is a specialized implementation of the {@see Context} class that
 * stores information about the operating system of the server.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
class ServerOsContext extends Context
{
    /**
     * @var OptionsResolver The options resolver
     */
    private $resolver;

    /**
     * {@inheritdoc}
     *
     * @throws UndefinedOptionsException If any of the options are not supported
     *                                   by the context
     * @throws InvalidOptionsException   If any of the options don't fulfill the
     *                                   specified validation rules
     */
    public function __construct(array $data = [])
    {
        $this->resolver = new OptionsResolver();

        $this->configureOptions($this->resolver);

        parent::__construct($this->resolver->resolve($data));
    }

    /**
     * {@inheritdoc}
     *
     * @throws UndefinedOptionsException If any of the options are not supported
     *                                   by the context
     * @throws InvalidOptionsException   If any of the options don't fulfill the
     *                                   specified validation rules
     */
    public function merge(array $data, bool $recursive = false): void
    {
        $data = $this->resolver->resolve($data);

        parent::merge($data, $recursive);
    }

    /**
     * {@inheritdoc}
     *
     * @throws UndefinedOptionsException If any of the options are not supported
     *                                   by the context
     * @throws InvalidOptionsException   If any of the options don't fulfill the
     *                                   specified validation rules
     */
    public function setData(array $data): void
    {
        $data = $this->resolver->resolve($data);

        parent::setData($data);
    }

    /**
     * {@inheritdoc}
     *
     * @throws UndefinedOptionsException If any of the options are not supported
     *                                   by the context
     * @throws InvalidOptionsException   If any of the options don't fulfill the
     *                                   specified validation rules
     */
    public function replaceData(array $data): void
    {
        $data = $this->resolver->resolve($data);

        parent::replaceData($data);
    }

    /**
     * {@inheritdoc}
     *
     * @throws UndefinedOptionsException If any of the options are not supported
     *                                   by the context
     * @throws InvalidOptionsException   If any of the options don't fulfill the
     *                                   specified validation rules
     */
    public function offsetSet($offset, $value): void
    {
        $data = $this->resolver->resolve([$offset => $value]);

        parent::offsetSet($offset, $data[$offset]);
    }

    /**
     * Gets the name of the operating system.
     */
    public function getName(): string
    {
        return $this->data['name'];
    }

    /**
     * Sets the name of the operating system.
     *
     * @param string $name The name
     */
    public function setName(string $name): void
    {
        $this->offsetSet('name', $name);
    }

    /**
     * Gets the version of the operating system.
     */
    public function getVersion(): string
    {
        return $this->data['version'];
    }

    /**
     * Sets the version of the operating system.
     *
     * @param string $version The version
     */
    public function setVersion(string $version): void
    {
        $this->offsetSet('version', $version);
    }

    /**
     * Gets the build of the operating system.
     */
    public function getBuild(): string
    {
        return $this->data['build'];
    }

    /**
     * Sets the build of the operating system.
     *
     * @param string $build The build
     */
    public function setBuild(string $build): void
    {
        $this->offsetSet('build', $build);
    }

    /**
     * Gets the version of the kernel of the operating system.
     */
    public function getKernelVersion(): string
    {
        return $this->data['kernel_version'];
    }

    /**
     * Sets the version of the kernel of the operating system.
     *
     * @param string $kernelVersion The kernel version
     */
    public function setKernelVersion(string $kernelVersion): void
    {
        $this->offsetSet('kernel_version', $kernelVersion);
    }

    /**
     * Configures the options of the context.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'name' => php_uname('s'),
            'version' => php_uname('r'),
            'build' => php_uname('v'),
            'kernel_version' => php_uname('a'),
        ]);

        $resolver->setAllowedTypes('name', 'string');
        $resolver->setAllowedTypes('version', 'string');
        $resolver->setAllowedTypes('build', 'string');
        $resolver->setAllowedTypes('kernel_version', 'string');
    }
}
