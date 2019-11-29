<?php

declare(strict_types=1);

namespace Sentry\Integration;

use Jean85\PrettyVersions;
use PackageVersions\Versions;
use Sentry\Event;
use Sentry\SentrySdk;
use Sentry\State\Scope;

/**
 * This integration logs with the event details all the versions of the packages
 * installed with Composer; the root project is included too.
 */
final class ModulesIntegration implements IntegrationInterface
{
    /**
     * @var array The list of installed vendors
     */
    private static $loadedModules = [];

    /**
     * {@inheritdoc}
     */
    public function setupOnce(): void
    {
        Scope::addGlobalEventProcessor(function (Event $event) {
            $integration = SentrySdk::getCurrentHub()->getIntegration(self::class);

            // The integration could be bound to a client that is not the one
            // attached to the current hub. If this is the case, bail out
            if ($integration instanceof self) {
                self::applyToEvent($integration, $event);
            }

            return $event;
        });
    }

    /**
     * Applies the information gathered by this integration to the event.
     *
     * @param self  $self  The instance of this integration
     * @param Event $event The event that will be enriched with the modules
     */
    public static function applyToEvent(self $self, Event $event): void
    {
        if (empty(self::$loadedModules)) {
            foreach (Versions::VERSIONS as $package => $rawVersion) {
                self::$loadedModules[$package] = PrettyVersions::getVersion($package)->getPrettyVersion();
            }
        }

        $event->setModules(self::$loadedModules);
    }
}
