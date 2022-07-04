<?php

namespace AutoSprint;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\PlayerAuthInputFlags;
use pocketmine\utils\TextFormat as Fart;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

public static array $sprint = [];

    public function onEnable() : void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

	public function onCommand(CommandSender $player, Command $command, string $label, array $args): bool {
	    $name = $player->getName();
        switch(strtolower($command->getName())) {
            case "autosprint":
            case "as":
                if (!$player instanceof Player) {
                $player->sendMessage(Fart::DARK_RED . "Use in-game!");
                }else{
                if(!in_array($name, self::$sprint)) {
                    self::$sprint[] = $name;
                    $player->sendMessage(Fart::GREEN . "You have enabled AutoSprint!");
                }else{
                    unset(self::$sprint[array_search($name, self::$sprint)]);
                    $player->sendMessage(Fart::DARK_RED . "You have disabled AutoSprint!");
                    }
                }
        }
        return true;
    }
            
    public function onPacketReceive(DataPacketReceiveEvent $event)
    {
        $player = $event->getOrigin()->getPlayer();
        $packet = $event->getPacket();
        if ($event->getPacket()->pid() === PlayerAuthInputPacket::NETWORK_ID) {
            if (in_array($player->getName(), self::$sprint)) {
                if($player->isSprinting() && $packet->hasFlag(PlayerAuthInputFlags::DOWN)){
                            $player->setSprinting(false);
                }elseif(!$player->isSprinting() && $packet->hasFlag(PlayerAuthInputFlags::UP)){
                            $player->setSprinting();
                        }
                }
        }
    }
}
