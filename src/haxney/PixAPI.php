<?php

namespace haxney;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

class PixAPI extends PluginBase implements Listener {

    private static $instance = null;

    public static function getInstance() : self {
        return self::$instance;
    }

    // dados importantes
    private Account $account;
    private Provider $provider;
    private Properties $properties;

    public function onLoad() : void {
        CurrencyFormat::init();
        self::$instance = $this;
    }

    public function onEnable() : void {
        $this->properties = new Properties($folder = $this->getDataFolder());
        $this->account = new JsonAccount($folder);
        $this->provider = new JsonProvider($folder);

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->registerAll('PixAPI', [
            'mycurrency' => new MyCurrencyCommand($this->properties->getString('cmd.mycurrency') ?? 'mycurrency', $this),
            'myformat' => new MyFormatCommand($this->properties->getString('cmd.myformat') ?? 'myformat', $this)
        ]);
    }

    public function onDisable() : void {
        $this->provider->save();
        $this->account->save();
    }

    public function existsAccount($player) : bool {
        return $this->provider->existsAccount($player);
    }

    public function myCurrency($player) : Int|Float {
        return $this->provider->myCurrency($player instanceof Player ? $player->getXuid() : $this->account->myXuid($player));
    }

    public function hasCurrency($player, Int|Float $amount) : bool {
        return $this->provider->hasCurrency($player instanceof Player ? $player->getXuid() : $this->account->myXuid($player), $amount);
    }
}