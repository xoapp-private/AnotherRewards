<?php

namespace xoapp\rewards\extension;

use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\item\VanillaItems;
use xoapp\rewards\formatter\TimeFormatter;
use xoapp\rewards\library\serializer\Serializer;

class CustomReward {

    public function __construct(
        private readonly string $name,
        private string $countdown = "1d",
        private ?int $inventory_slot = null,
        private ?Item $inventory_item = null,
        private array $contents = [],
        private array $claimers = []
    ) {}

    public function getName(): string {
        return $this->name;
    }

    public function getCountdown(): string {
        return $this->countdown;
    }

    public function getContents(): array {
        return $this->contents;
    }

    public function getInventorySlot(): ?int {
        return $this->inventory_slot;
    }

    public function getInventoryItem(): ?Item {

        if (is_null($this->inventory_item)) {
            return VanillaItems::AIR();
        }

        return clone $this->inventory_item;
    }

    public function getClaimers(): array {
        return $this->claimers;
    }

    public function addClaimer(Player $player): void {
        $this->claimers[$player->getName()] = $this->parseCountdown();

        $inventory = $player->getInventory();

        foreach ($this->contents as $content) {
            /** @var $content Item */

            if (!$inventory->canAddItem($content)) {
                $player->getWorld()->dropItem($player->getPosition(), $content);
                continue;
            }

            $inventory->addItem($content);
        }

        $player->sendMessage(
            TextFormat::colorize("&aYou successfully claimed &e" . sizeof($this->contents) . " &aRewards")
        );
    }

    public function isClaimer(Player $player): bool {
        return isset($this->claimers[$player->getName()]);
    }

    public function deleteClaimer(Player $player): void {
        unset($this->claimers[$player->getName()]);
    }

    public function getClaimer(Player $player): int {
        return $this->claimers[$player->getName()];
    }

    public function setContents(array $contents): void {
        $this->contents = $contents;
    }

    public function setCountdown(string $countdown): void {
        $this->countdown = $countdown;
    }

    public function setInventorySlot(?int $inventory_slot): void {
        $this->inventory_slot = $inventory_slot;
    }

    public function setInventoryItem(?Item $inventory_item): void {
        $this->inventory_item = $inventory_item;
    }

    public function parseCountdown(): int {
        return TimeFormatter::parseTime($this->countdown);
    }

    public function jsonSerialize(): array {
        return [
            "countdown" => $this->countdown,
            "inventory_slot" => $this->inventory_slot,
            "inventory_item" => is_null($this->inventory_item) ? null : Serializer::serializeItem($this->inventory_item),
            "contents" => Serializer::serialize($this->contents),
            "claimers" => serialize($this->claimers)
        ];
    }
}