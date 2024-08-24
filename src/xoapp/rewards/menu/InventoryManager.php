<?php

namespace xoapp\rewards\menu;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\inventory\Inventory;
use xoapp\rewards\factory\RewardFactory;
use xoapp\rewards\extension\CustomReward;
use xoapp\rewards\formatter\TimeFormatter;
use xoapp\rewards\library\muqsit\invmenu\InvMenu;
use xoapp\rewards\library\muqsit\invmenu\type\InvMenuTypeIds;
use xoapp\rewards\library\muqsit\invmenu\transaction\InvMenuTransaction;
use xoapp\rewards\library\muqsit\invmenu\transaction\InvMenuTransactionResult;

class InventoryManager {

    public static function getRewards(Player $player): void {

        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);

        $menu->setName("Available Rewards");

        $rewards = RewardFactory::getRewards();

        $inventory = $menu->getInventory();

        foreach ($rewards as $key => $reward) {

            $item = $reward->getInventoryItem();

            $item->setNamedTag(
                CompoundTag::create()->setString("reward", $key)
            );

            $item->setCustomName(
                TextFormat::colorize("&f- &6" . $key)
            );

            $item->setLore(
                array_map(
                    fn (string $line) => TextFormat::colorize($line),
                    [
                        " ",
                        " &fCooldown: &e" . TimeFormatter::getTimeLeft($reward->parseCountdown()),
                        " &fPossible Contents: &b" . sizeof($reward->getContents()),
                        " ",
                        " &fClaimers: &3" . sizeof($reward->getClaimers()),
                        " ",
                        "&7&o Tap To Claim"
                    ]
                )
            );

            $slot = $reward->getInventorySlot();

            if (is_null($slot)) {
                $inventory->addItem($item);
            } else {
                $inventory->setItem($slot, $item);
            }

        }

        $menu->setListener(
            function (InvMenuTransaction $transaction): InvMenuTransactionResult {

                $player = $transaction->getPlayer();

                $item = $transaction->getItemClicked();

                $nbt = $item->getNamedTag()->getTag("reward");

                if (is_null($nbt)) {
                    return $transaction->discard();
                }

                $reward = RewardFactory::get($nbt->getValue());

                if (is_null($reward)) {
                    return $transaction->discard();
                }

                if ($reward->isClaimer($player)) {
                    $left = TimeFormatter::getTimeLeft(
                        $reward->getClaimer($player)
                    );

                    if ($left > 0) {
                        $player->sendMessage(TextFormat::colorize("&cYou cant claim this reward until &e" . $left));
                        $player->removeCurrentWindow();
                        return $transaction->discard();
                    }

                    $reward->deleteClaimer($player);
                }

                $reward->addClaimer($player);

                $player->removeCurrentWindow();

                return $transaction->discard();
            }
        );

        $menu->send($player);
    }

    public static function editSlots(Player $player): void {

        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);

        $menu->setName("Slot Editor");

        $rewards = RewardFactory::getRewards();

        $inventory = $menu->getInventory();

        foreach ($rewards as $key => $reward) {

            $item = $reward->getInventoryItem();

            $item->setNamedTag(
                CompoundTag::create()->setString("reward", $key)
            );

            $item->setCustomName(
                TextFormat::colorize("&f- &6" . $key)
            );

            $slot = $reward->getInventorySlot();

            if (is_null($slot)) {
                $inventory->addItem($item);
            } else {
                $inventory->setItem($slot, $item);
            }
        }

        $menu->setInventoryCloseListener(
            function (Player $player, Inventory $inventory): void {

                foreach ($inventory->getContents() as $slot => $item) {

                    $nbt = $item->getNamedTag()->getTag("reward");

                    if (is_null($nbt)) {
                        continue;
                    }

                    $reward = RewardFactory::get($nbt->getValue());

                    if (is_null($reward)) {
                        continue;
                    }

                    $reward->setInventorySlot($slot);
                }

                $player->sendMessage(TextFormat::colorize("&aYou successfully updated the item slots"));
            }
        );

        $menu->send($player);
    }

    public static function editContents(Player $player, CustomReward $reward): void {

        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);

        $menu->setName("Contents Editor");

        $menu->getInventory()->setContents(
            $reward->getContents()
        );

        $menu->setInventoryCloseListener(
            function (Player $player, Inventory $inventory) use ($reward) : void {

                $reward->setContents(
                    $inventory->getContents()
                );

                $player->sendMessage(TextFormat::colorize("&aYou successfully updated the contents"));
            }
        );

        $menu->send($player);
    }
}