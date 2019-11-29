<?php

declare(strict_types=1);

namespace Sentry\Monolog;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Sentry\Severity;
use Sentry\State\HubInterface;
use Sentry\State\Scope;

/**
 * This Monolog handler logs every message to a Sentry's server using the given
 * hub instance.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class Handler extends AbstractProcessingHandler
{
    /**
     * @var HubInterface
     */
    private $hub;

    /**
     * Constructor.
     *
     * @param HubInterface $hub    The hub to which errors are reported
     * @param int          $level  The minimum logging level at which this
     *                             handler will be triggered
     * @param bool         $bubble Whether the messages that are handled can
     *                             bubble up the stack or not
     */
    public function __construct(HubInterface $hub, $level = Logger::DEBUG, bool $bubble = true)
    {
        $this->hub = $hub;

        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record): void
    {
        $payload = [
            'level' => $this->getSeverityFromLevel($record['level']),
            'message' => $record['message'],
            'logger' => 'monolog.' . $record['channel'],
        ];

        if (isset($record['context']['exception']) && $record['context']['exception'] instanceof \Throwable) {
            $payload['exception'] = $record['context']['exception'];
        }

        $this->hub->withScope(function (Scope $scope) use ($record, $payload): void {
            $scope->setExtra('monolog.channel', $record['channel']);
            $scope->setExtra('monolog.level', $record['level_name']);

            if (isset($record['context']['extra']) && \is_array($record['context']['extra'])) {
                foreach ($record['context']['extra'] as $key => $value) {
                    $scope->setExtra((string) $key, $value);
                }
            }

            if (isset($record['context']['tags']) && \is_array($record['context']['tags'])) {
                foreach ($record['context']['tags'] as $key => $value) {
                    $scope->setTag($key, $value);
                }
            }

            $this->hub->captureEvent($payload);
        });
    }

    /**
     * Translates the Monolog level into the Sentry severity.
     *
     * @param int $level The Monolog log level
     */
    private function getSeverityFromLevel(int $level): Severity
    {
        switch ($level) {
            case Logger::DEBUG:
                return Severity::debug();
            case Logger::INFO:
            case Logger::NOTICE:
                return Severity::info();
            case Logger::WARNING:
                return Severity::warning();
            case Logger::ERROR:
                return Severity::error();
            case Logger::CRITICAL:
            case Logger::ALERT:
            case Logger::EMERGENCY:
                return Severity::fatal();
            default:
                return Severity::info();
        }
    }
}
