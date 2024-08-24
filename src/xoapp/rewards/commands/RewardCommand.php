<?php

namespace xoapp\rewards\commands;

use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use xoapp\rewards\menu\FormManager;
use pocketmine\command\CommandSender;
use xoapp\rewards\menu\InventoryManager;
use xoapp\rewards\factory\RewardFactory;

class RewardCommand extends Command {

    public function __construct() {
        parent::__construct("reward");

        $this->setPermission("another.rewards.command");

        $this->setAliases(
            ["rewards"]
        );
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): void {

        if (!$player instanceof Player) {
            return;
        }

        if (!$this->testPermissionSilent($player)) {
            return;
        }

        if (isset($args[0])) {

            if (!$player->hasPermission("another.rewards.admin")) {
                InventoryManager::getRewards($player);
                return;
            }

            if ($args[0] == "help") {
                $messages = [
                    "&eUsage /reward create",
                    "&eUsage /reward delete",
                    "&eUsage /reward edit (rewardName)",
                    "&eUsage /reward contents (rewardName)",
                    "&eUsage /reward slots",
                ];

                $player->sendMessage(
                    TextFormat::colorize(implode("\n", $messages))
                );

                return;
            }

            if ($args[0] == "create") {
                $player->sendForm(FormManager::createReward());
                return;
            }

            if ($args[0] == "delete") {
                $player->sendForm(FormManager::deleteReward());
                return;
            }

            if ($args[0] == "edit") {
                if (!isset($args[1])) {
                    $player->sendMessage(TextFormat::colorize("&cUsage /reward edit (rewardName)"));
                    return;
                }

                $reward = RewardFactory::get($args[1]);

                if (is_null($reward)) {
                    $player->sendMessage(TextFormat::colorize("&cThis reward not exists"));
                    return;
                }

                $player->sendForm(FormManager::editReward($reward));
                return;
            }

            if ($args[0] == "contents") {
                if (!isset($args[1])) {
                    $player->sendMessage(TextFormat::colorize("&cUsage /reward contents (rewardName)"));
                    return;
                }

                $reward = RewardFactory::get($args[1]);

                if (is_null($reward)) {
                    $player->sendMessage(TextFormat::colorize("&cThis reward not exists"));
                    return;
                }

                InventoryManager::editContents($player, $reward);
                return;
            }

            if ($args[0] == "slots") {
                InventoryManager::editSlots($player);
                return;
            }
        }

        InventoryManager::getRewards($player);
    }
}