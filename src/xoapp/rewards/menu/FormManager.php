<?php

namespace xoapp\rewards\menu;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use xoapp\rewards\factory\RewardFactory;
use xoapp\rewards\extension\CustomReward;
use xoapp\rewards\formatter\TimeFormatter;
use xoapp\rewards\library\dktapps\pmforms\CustomForm;
use xoapp\rewards\library\dktapps\pmforms\element\Input;
use xoapp\rewards\library\dktapps\pmforms\element\Toggle;
use xoapp\rewards\library\dktapps\pmforms\element\Dropdown;
use xoapp\rewards\library\dktapps\pmforms\CustomFormResponse;

class FormManager {

    public static function deleteReward(): CustomForm {
        return new CustomForm(
            "Delete Reward",
            [
                new Dropdown(
                    "option", "Select Reward", array_keys(RewardFactory::getRewards())
                )
            ],
            function (Player $player, CustomFormResponse $response): void {

                $option = $response->getInt("option");

                $reward = array_values(RewardFactory::getRewards())[$option];

                if (!$reward instanceof CustomReward) {
                    $player->sendMessage(TextFormat::colorize("&cInvalid Reward"));
                    return;
                }

                RewardFactory::delete($reward->getName());

                $player->sendMessage(
                    TextFormat::colorize("&aYou Successfully deleted Reward " . $reward->getName())
                );
            }
        );
    }

    public static function createReward(): CustomForm {
        return new CustomForm(
            "Create Reward",
            [
                new Input("name", "Reward Name", "Example: Diamond"),
                new Input("countdown", "Reward Countdown", "Example: 22h (22 hours)")
            ],
            function (Player $player, CustomFormResponse $response): void {

                $name = $response->getString("name");
                $countdown = $response->getString("countdown");

                if (!TimeFormatter::isValidFormat($countdown)) {
                    $player->sendMessage(TextFormat::colorize("&cSet a valid time format"));
                    return;
                }

                if (RewardFactory::exists($name)) {
                    $player->sendMessage(TextFormat::colorize("&cThis reward already exists"));
                    return;
                }

                RewardFactory::add(
                    new CustomReward($name, $countdown)
                );

                $player->sendMessage(TextFormat::colorize("&aYou successfully created Reward " . $name));
            }
        );
    }

    public static function editReward(CustomReward $reward): CustomForm {
        return new CustomForm(
            "Edit Reward",
            [
                new Input(
                    "countdown", "Reward Countdown", "Example: 22h (22 hours)", $reward->getCountdown()
                ),
                new Toggle(
                    "item", "Edit Inventory Item?", false
                )
            ],
            function (Player $player, CustomFormResponse $response) use ($reward) : void {

                $countdown = $response->getString("countdown");
                $choice = $response->getBool("item");

                if (!TimeFormatter::isValidFormat($countdown)) {
                    $player->sendMessage(TextFormat::colorize("&cSet a valid time format"));
                    return;
                }

                if (!RewardFactory::exists($reward->getName())) {
                    $player->sendMessage(TextFormat::colorize("&cThis reward not exists"));
                    return;
                }

                $reward->setCountdown($countdown);

                $player->sendMessage(
                    TextFormat::colorize("&aYou successfully edited countdown Reward " . $reward->getName())
                );

                if (!$choice) {
                    return;
                }

                $item = $player->getInventory()->getItemInHand();

                if ($item->isNull()) {
                    $player->sendMessage(TextFormat::colorize("&cInvalid hand item"));
                    return;
                }

                $reward->setInventoryItem($item);

                $player->sendMessage(
                    TextFormat::colorize("&aYou successfully edited item Reward " . $reward->getName())
                );
            }
        );
    }
}