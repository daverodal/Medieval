<?php
namespace Wargame\Medieval\Montaperti1260;
use Wargame\Medieval\MedievalLandBattle;
use Wargame\Medieval\MedievalUnit;
use Wargame\Medieval\UnitFactory;
use Wargame\FacingMoveRules;
/**
 *
 * Copyright 2012-2015 David Rodal
 * User: David Markarian Rodal
 * Date: 3/8/15
 * Time: 5:48 PM
 *
 *  This program is free software; you can redistribute it
 *  and/or modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */



class Montaperti1260 extends MedievalLandBattle
{
    
    const GHILBELLINI_FORCE = 1;
    const GUELFI_FORCE = 2;
    /* a comment */

    public $specialHexesMap = ['SpecialHexA'=>1, 'SpecialHexB'=>2, 'SpecialHexC'=>1];

    public static function buildUnit($data = false){
        return UnitFactory::build($data);
    }

    static function getPlayerData($scenario){
        $forceName = ["Neutral Observer", "Ghilbellini", "Guelfi"];
        return \Wargame\Battle::register($forceName,
            [$forceName[0], $forceName[2], $forceName[1]]);
    }

    function terrainGen($mapDoc, $terrainDoc)
    {
        $this->terrain->addTerrainFeature("orchard", "orchard", "t", 0, 0,0, false, true);

        parent::terrainGen($mapDoc, $terrainDoc);
        $this->terrain->addTerrainFeature("town", "town", "t", 0, 0, 1, false);
    }
    function save()
    {
        $data = parent::save();
        $data->specialHexA = $this->specialHexA;
        $data->specialHexB = $this->specialHexB;
        return $data;
    }

    public function scenInit(){


        $scenario = $this->scenario;
        $unitSets = $scenario->units;

        foreach($unitSets as $unitSet) {
//            dd($unitSet);
            if($unitSet->forceId !== Montaperti1260::GHILBELLINI_FORCE){
                continue;
            }
            for ($i = 0; $i < $unitSet->num; $i++) {
                if($unitSet->hq){
                    UnitFactory::create("lll", $unitSet->forceId, "deployBox", $unitSet->combat, $unitSet->movement, $unitSet->commandRadius, STATUS_CAN_DEPLOY,  $unitSet->reinforce, 1,  $unitSet->nationality,  "hq", 1, $unitSet->facing, $unitSet->armorClass, $unitSet->bow,MedievalUnit::BATTLE_READY, $unitSet->steps);
                }else{
                    UnitFactory::create("lll", $unitSet->forceId, "deployBox", $unitSet->combat, $unitSet->movement, $unitSet->range, STATUS_CAN_DEPLOY,  $unitSet->reinforce, 1,  $unitSet->nationality,  $unitSet->class, 1, $unitSet->facing, $unitSet->armorClass, $unitSet->bow);
                }
            }
        }
        foreach($unitSets as $unitSet) {
//            dd($unitSet);
            if($unitSet->forceId !== Montaperti1260::GUELFI_FORCE){
                continue;
            }
            for ($i = 0; $i < $unitSet->num; $i++) {
                if($unitSet->hq){
                    UnitFactory::create("lll", $unitSet->forceId, "deployBox", $unitSet->combat, $unitSet->movement, $unitSet->commandRadius, STATUS_CAN_DEPLOY,  $unitSet->reinforce, 1,  $unitSet->nationality, "hq", 1, $unitSet->facing, $unitSet->armorClass, $unitSet->bow,MedievalUnit::BATTLE_READY, $unitSet->steps );
                }else{
                    UnitFactory::create("lll", $unitSet->forceId, "deployBox", $unitSet->combat, $unitSet->movement, $unitSet->range, STATUS_CAN_DEPLOY,  $unitSet->reinforce, 1,  $unitSet->nationality,  $unitSet->class, 1, $unitSet->facing, $unitSet->armorClass, $unitSet->bow);
                }
            }
        }
    }
    public function init()
    {
        UnitFactory::$injector = $this->force;
        $scenario = $this->scenario;

        if(isset($scenario->units)){
            return $this->scenInit();
        }


        $baseValue = 6;
        $reducedBaseValue = 3;
        if(!empty($scenario->weakerLoyalist)){
            $baseValue = 5;
            $reducedBaseValue = 2;
        }
        if(!empty($scenario->strongerLoyalist)){
            $baseValue = 7;
        }

        UnitFactory::create("lll", self::GHILBELLINI_FORCE, "deployBox",  3, 5,2,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);
        UnitFactory::create("lll", self::GHILBELLINI_FORCE, "deployBox",  2, 5,2,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);
        UnitFactory::create("lll", self::GHILBELLINI_FORCE, "deployBox",  2, 5,2,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);

        for($i = 0;$i < 9;$i++){
            UnitFactory::create("lll", self::GHILBELLINI_FORCE, "deployBox", 6, 5,1,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'cavalry',1, 0, 'H');

        }

        for($i = 0;$i < 1;$i++){
            UnitFactory::create("lll", self::GHILBELLINI_FORCE, "deployBox", 4,  3,1,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'inf',1, 0, 'M');

        }

        for($i = 0;$i < 2;$i++){
            UnitFactory::create("lll", self::GHILBELLINI_FORCE, "deployBox", 1, 3,2,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'inf',1, false, 'S', true);

        }

        UnitFactory::create("lll", self::GUELFI_FORCE, "deployBox",  2, 5,2,  STATUS_CAN_DEPLOY, "B", 1, "crusader", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);
        UnitFactory::create("lll", self::GUELFI_FORCE, "deployBox",  1, 5,2,  STATUS_CAN_DEPLOY, "B", 1, "crusader", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);
        UnitFactory::create("lll", self::GUELFI_FORCE, "deployBox",  1, 5,2,  STATUS_CAN_DEPLOY, "B", 1, "crusader", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);


        for($i = 0;$i < 8;$i++) {
            UnitFactory::create("lll", self::GUELFI_FORCE, "deployBox",  4, 5,1,  STATUS_CAN_DEPLOY, "B", 1, "crusader", 'cavalry',1, 3, 'H');

        }
        for($i = 0;$i < 6;$i++) {
            UnitFactory::create("lll", self::GUELFI_FORCE, "deployBox",  3, 3,1,  STATUS_CAN_DEPLOY, "B", 1, "crusader", 'inf',1, 3, 'M');

        }
        for($i = 0;$i < 2;$i++) {
            UnitFactory::create("lll", self::GUELFI_FORCE, "deployBox",  2, 4,2,  STATUS_CAN_DEPLOY, "B", 1, "crusader", 'inf',1, 3, 'M', true);

        }

        UnitFactory::create("lll", self::GUELFI_FORCE, "deployBox",  2, 3,1,  STATUS_CAN_DEPLOY, "B", 1, "crusader", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);

        for($i = 0;$i < 2;$i++) {
            UnitFactory::create("lll", self::GUELFI_FORCE, "deployBox",  7, 3,1,  STATUS_CAN_DEPLOY, "B", 1, "crusader", 'inf',1, 3, 'K');

        }

    }

    public static function myName(){
        echo __CLASS__;
    }

    function __construct($data = null, $arg = false, $scenario = false)
    {

        parent::__construct($data, $arg, $scenario);

        $crt = new \Wargame\Medieval\MedievalCombatResultsTable();
        $this->combatRules->injectCrt($crt);

//        $this->gameRules->gameHasCombatResolutionMode = false;
        if ($data) {
            $this->specialHexA = $data->specialHexA;
            $this->specialHexB = $data->specialHexB;
        } else {

            $this->victory = new \Wargame\Victory("Wargame\\Medieval\\Montaperti1260\\VictoryCore");
            
            // game data
            $this->gameRules->setMaxTurn(8);
            $this->deployFirstMoveSecond();
        }
    }
}