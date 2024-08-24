<?php

declare(strict_types=1);

namespace xoapp\rewards\library\muqsit\invmenu\type;

use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use xoapp\rewards\library\muqsit\invmenu\InvMenu;
use xoapp\rewards\library\muqsit\invmenu\type\graphic\InvMenuGraphic;

interface InvMenuType{

	public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic;

	public function createInventory() : Inventory;
}