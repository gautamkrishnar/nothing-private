<?php

declare(strict_types=1);

namespace Sentry\Integration;

/**
 * This class handles the state of already installed integrations.
 * It makes sure to call {@link IntegrationInterface::setupOnce} only once per integration.
 */
final class Handler
{
    /**
     * @var array<string, bool> The registered integrations
     */
    private static $integrations = [];

    /**
     * Calls {@link IntegrationInterface::setupOnce} for all passed integrations if it hasn't been called yet.
     *
     * @param array $integrations The integrations
     *
     * @return array<string, IntegrationInterface>
     */
    public static function setupIntegrations(array $integrations): array
    {
        $integrationIndex = [];

        /* @var IntegrationInterface $integration */
        foreach ($integrations as $integration) {
            $className = \get_class($integration);

            if (!$integration instanceof IntegrationInterface) {
                throw new \InvalidArgumentException(sprintf('Expecting integration implementing %s interface, got %s', IntegrationInterface::class, $className));
            }

            if (!isset(self::$integrations[$className])) {
                self::$integrations[$className] = true;

                $integration->setupOnce();
            }

            $integrationIndex[$className] = $integration;
        }

        return $integrationIndex;
    }
}
