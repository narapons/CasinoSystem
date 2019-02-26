<?php

namespace CasinoSystem;

use pocketmine\Player;
use pocketmine\Plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;//Config関連
use pocketmine\utils\Config;//Config関連
use onebone\economyapi\EconomyAPI;//EconomyAPI
use pocketmine\command\Command;//以下コマンド関連
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecuter;
use pocketmine\command\ConsoleCommandSender;

class Main extends PluginBase implements Listener{

    //サーバー起動時の処理
	public function onEnable(){
	$this->getServer()->getPluginManager()->registerEvents($this, $this);
	$this->Config = new Config($this->getDataFolder() . "Config.yml", Config::YAML, array(
	'Message1' => '[Casino] §e当たりです！',
	'Message2' => '[Casino] §eハズレです！'
	));//コンフィグの作成
	}

    //コマンド使用時の処理
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args):bool{
        if($sender->getName() === "CONSOLE"){//コンソールから実行した場合
            $sender->sendMessage(">>§cこのコマンドはゲーム内で使ってください");
            return false;
        }else{ //コンソールからの実行ではない場合
            switch($command->getName()){
                case "casino"://コマンドがcasinoだった場合
                $name = $sender->getName();//コマンド実行者の名前を取得
                $economy = EconomyAPI::getInstance();//EconomyAPIのインスタンスを作成
                $PlayerHasMoney = EconomyAPI::getInstance()->myMoney($name);//コマンド実行者の所持金を取得
                if($sender instanceof Player){//コマンド実行者がプレイヤーだった場合
                    if(!isset($args[0])){//掛け金が入力されていない場合
                        $sender->sendMessage(">>§c掛け金を入力してください");
                        $sender->sendMessage("use: /casino <掛け金>");
                    }else{//掛け金が入力されてた場合の処理
                        if(is_numeric($args[0]) || $args[0] >= 0){//掛け金が数字or0以上だった場合
                            if($PlayerHasMoney <= $args[0]){//所持金が掛け金以上ではない場合
                                $sender->sendMessage(">>§c所持金が不足しています");
                                $sender->sendMessage(">>§c掛け金を所持金以下にしてください");
                            }else{//所持金が掛け金以上の場合
                                $economy->reduceMoney($name, $args[0]);//掛け金を所持金から減らす
                                $r = rand(0, 1);//変数に関数randを使い代入(確率設定)
                                if($r == 0){//確率設定(？)
                                    $sender->sendMessage($this->Config->get("Message1"));
                                    $economy->addMoney($name, $args[0] * 2); //当たりだった場合
                                }else{//確率設定(？)
                                    $sender->sendMessage($this->Config->get("Message2"));
                                    //ハズレだった場合
                                }
                            }
                        }else{//掛け金が数字or0以上ではない場合
                            $sender->sendMessage(">>§c掛け金として0以上の数字を入力してください");
                            $sender->sendMessage("use: /casino <掛け金>");
                        }
                    }
                }
            }
        }
	return true;
    }
}
