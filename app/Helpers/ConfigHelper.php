<?php
declare(strict_types=1);

namespace App\Helpers;

class ConfigHelper
{
    private static ?array $settings = null;

    /**
     * Load settings from database once per request.
     */
    private static function loadSettings(): void
    {
        if (self::$settings === null) {
            self::$settings = [];
            try {
                $rows = Database::all("SELECT config_key, config_value FROM system_settings");
                foreach ($rows as $row) {
                    self::$settings[$row['config_key']] = $row['config_value'];
                }
            } catch (\Throwable $e) {
                Logger::error("Failed to load system settings from database: " . $e->getMessage());
            }
        }
    }

    /**
     * Fetch a configuration value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::loadSettings();
        return self::$settings[$key] ?? $default;
    }

    /**
     * Set/Update a configuration value.
     */
    public static function set(string $key, ?string $value): bool
    {
        try {
            $sql = "INSERT INTO system_settings (config_key, config_value, updated_at) 
                    VALUES (:key, :value, NOW())
                    ON DUPLICATE KEY UPDATE config_value = :value_update, updated_at = NOW()";
            
            $success = Database::execute($sql, [
                'key' => $key,
                'value' => $value,
                'value_update' => $value
            ]);

            if ($success) {
                self::loadSettings();
                self::$settings[$key] = $value;
            }

            return $success;
        } catch (\Throwable $e) {
            Logger::error("Failed to update system setting '{$key}': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieve all settings.
     */
    public static function all(): array
    {
        self::loadSettings();
        return self::$settings ?? [];
    }
}
