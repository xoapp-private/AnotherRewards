<?php

namespace xoapp\rewards;

use pocketmine\plugin\PluginBase;
use xoapp\rewards\data\DataManager;
use pocketmine\utils\SingletonTrait;
use xoapp\rewards\factory\RewardFactory;
use xoapp\rewards\commands\RewardCommand;
use xoapp\rewards\library\muqsit\invmenu\InvMenuHandler;

class Loader extends PluginBase {

    use SingletonTrait {
        setInstance as private;
        reset as private;
    }

    protected function onEnable(): void {

        self::setInstance($this);

        DataManager::load();
        RewardFactory::load();

        $this->getServer()->getCommandMap()->register("reward", new RewardCommand());

        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
    }

    protected function onDisable(): void {
        RewardFactory::saveAll();
    }
}