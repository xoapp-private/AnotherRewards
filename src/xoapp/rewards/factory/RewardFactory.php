<?php

namespace xoapp\rewards\factory;

use xoapp\rewards\data\DataManager;
use xoapp\rewards\extension\CustomReward;
use xoapp\rewards\library\serializer\Serializer;

class RewardFactory {

    /** @var CustomReward[]  */
    private static array $rewards = [];

    public static function load(): void {
        $saved = DataManager::getSavedData();

        foreach ($saved as $key => $data) {

            $reward = new CustomReward(
                $key,
                $data["countdown"],
                $data["inventory_slot"],
                Serializer::deserializeItem($data["inventory_item"]),
                Serializer::deserialize($data["contents"]),
                unserialize($data["claimers"])
            );

            self::add($reward);
        }
    }

    public static function add(CustomReward $reward): void {
        self::$rewards[$reward->getName()] = $reward;
    }

    public static function get(string $name): ?CustomReward {
        return self::$rewards[$name] ?? null;
    }

    public static function delete(string $name): void {
        unset(self::$rewards[$name]);
    }

    public static function exists(string $name): bool {
        return isset(self::$rewards[$name]);
    }

    public static function getRewards(): array {
        return self::$rewards;
    }

    public static function saveAll(): void {
        foreach (self::$rewards as $key => $reward) {
            DataManager::setData($key, $reward->jsonSerialize());
        }
    }
}