<?php

namespace xoapp\rewards\data;

use xoapp\rewards\Loader;
use pocketmine\utils\Config;

class DataManager {

    private static Config $config;

    public static function load(): void {
        self::$config = new Config(
            Loader::getInstance()->getDataFolder() . "rewards.json", Config::JSON
        );
    }

    public static function setData(string $key, mixed $data): void {
        self::$config->set($key, $data);
        self::$config->save();
    }

    public static function unsetData(string $key): void {
        self::$config->remove($key);
        self::$config->save();
    }

    public static function getSavedData(): array {
        return self::$config->getAll();
    }
}