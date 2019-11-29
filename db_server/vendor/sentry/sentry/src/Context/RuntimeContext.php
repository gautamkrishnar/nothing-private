<?php

declare(strict_types=1);

namespace Sentry\Context;

use Sentry\Util\PHPVersion;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class is a specialized implementation of the {@see Context} class that
 * stores information about the current runtime.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
class RuntimeContext extends Context
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
     * Gets the name of the runtime.
     */
    public function getName(): string
    {
        return $this->data['name'];
    }

    /**
     * Sets the name of the runtime.
     *
     * @param string $name The name
     */
    public function setName(string $name): void
    {
        $this->offsetSet('name', $name);
    }

    /**
     * Gets the version of the runtime.
     */
    public function getVersion(): string
    {
        return $this->data['version'];
    }

    /**
     * Sets the version of the runtime.
     *
     * @param string $version The version
     */
    public function setVersion(string $version): void
    {
        $this->offsetSet('version', $version);
    }

    /**
     * Configures the options of the context.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'name' => 'php',
            'version' => PHPVersion::parseVersion(),
        ]);

        $resolver->setAllowedTypes('name', 'string');
        $resolver->setAllowedTypes('version', 'string');
    }
}
