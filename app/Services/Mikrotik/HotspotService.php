<?php

declare(strict_types=1);

namespace App\Services\Mikrotik;

/**
 * Hotspot Service - Manages Hotspot users on Mikrotik RouterOS.
 */
class HotspotService extends BaseMikrotikService
{
    /**
     * Get all hotspot users.
     */
    public function getAllUsers(): array
    {
        return $this->read('/ip/hotspot/user/print');
    }

    /**
     * Get a single hotspot user.
     */
    public function getUser(string $username): ?array
    {
        $results = $this->read('/ip/hotspot/user/print', ['name' => $username]);
        return $results[0] ?? null;
    }

    /**
     * Add a new hotspot user.
     */
    public function addUser(array $data): bool
    {
        $this->executeRetry(function () use ($data) {
            $this->add('/ip/hotspot/user/add', $data);
        });

        $this->logInfo('Hotspot user created', ['username' => $data['name'] ?? 'unknown']);
        return true;
    }

    /**
     * Update hotspot user.
     */
    public function updateUser(string $username, array $data): bool
    {
        $this->set('/ip/hotspot/user/set', ['name' => $username], $data);
        $this->logInfo('Hotspot user updated', ['username' => $username]);
        return true;
    }

    /**
     * Delete hotspot user.
     */
    public function deleteUser(string $username): bool
    {
        $this->remove('/ip/hotspot/user/remove', ['name' => $username]);
        $this->logInfo('Hotspot user deleted', ['username' => $username]);
        return true;
    }

    /**
     * Enable hotspot user.
     */
    public function enableUser(string $username): bool
    {
        $this->set('/ip/hotspot/user/set', ['name' => $username], ['disabled' => 'no']);
        return true;
    }

    /**
     * Disable hotspot user.
     */
    public function disableUser(string $username): bool
    {
        $this->set('/ip/hotspot/user/set', ['name' => $username], ['disabled' => 'yes']);
        return true;
    }

    /**
     * Get all active hotspot sessions.
     */
    public function getActiveSessions(): array
    {
        return $this->read('/ip/hotspot/active/print');
    }

    /**
     * Get active session for a specific user.
     */
    public function getActiveSession(string $username): ?array
    {
        $results = $this->read('/ip/hotspot/active/print', ['user' => $username]);
        return $results[0] ?? null;
    }

    /**
     * Disconnect an active hotspot session.
     */
    public function disconnectUser(string $username): bool
    {
        try {
            $this->remove('/ip/hotspot/active/remove', ['user' => $username]);
            return true;
        } catch (\Exception $e) {
            $this->logError('Failed to disconnect hotspot user', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get hotspot server profiles.
     */
    public function getServerProfiles(): array
    {
        return $this->read('/ip/hotspot/server/profile/print');
    }

    /**
     * Get hotspot servers.
     */
    public function getServers(): array
    {
        return $this->read('/ip/hotspot/server/print');
    }
}
