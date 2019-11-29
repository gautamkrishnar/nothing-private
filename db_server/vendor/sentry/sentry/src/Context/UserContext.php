<?php

declare(strict_types=1);

namespace Sentry\Context;

/**
 * This class is a specialized version of the base `Context` adapted to work
 * for the user context.
 */
final class UserContext extends Context
{
    /**
     * Gets the ID of the user.
     */
    public function getId(): ?string
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Sets the ID of the user.
     *
     * @param string|null $id The ID
     */
    public function setId(?string $id): void
    {
        $this->data['id'] = $id;
    }

    /**
     * Gets the username of the user.
     */
    public function getUsername(): ?string
    {
        return $this->data['username'] ?? null;
    }

    /**
     * Sets the username of the user.
     *
     * @param string|null $username The username
     */
    public function setUsername(?string $username): void
    {
        $this->data['username'] = $username;
    }

    /**
     * Gets the email of the user.
     */
    public function getEmail(): ?string
    {
        return $this->data['email'] ?? null;
    }

    /**
     * Sets the email of the user.
     *
     * @param string|null $email The email
     */
    public function setEmail(?string $email): void
    {
        $this->data['email'] = $email;
    }

    /**
     * Gets the ip address of the user.
     */
    public function getIpAddress(): ?string
    {
        return $this->data['ip_address'] ?? null;
    }

    /**
     * Sets the ip address of the user.
     *
     * @param string|null $ipAddress The ip address
     */
    public function setIpAddress(?string $ipAddress): void
    {
        $this->data['ip_address'] = $ipAddress;
    }
}
