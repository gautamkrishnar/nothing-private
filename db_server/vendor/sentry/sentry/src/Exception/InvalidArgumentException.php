<?php

declare(strict_types=1);

namespace Sentry\Exception;

/**
 * This class represents an exception thrown if an argument does not match with
 * the expected value.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface
{
}
