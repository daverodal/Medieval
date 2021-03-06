<?php
/**
 * Copyright 2016 David Rodal
 * User: David Markarian Rodal
 * Date: 2/28/16
 * Time: 1:34 PM
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Wargame\Medieval;

use Wargame\Hexagon;
use Wargame\Battle;
use stdClass;


class AncientUnit extends \Wargame\MovableUnit  implements \JsonSerializable
{
    const BATTLE_READY = 0;
    const DISORDERED = 1;
    /* L, M, H, K */
    public $armorClass;
    /* battle ready, reserve, disorganized */
    public $orgStatus;
    protected $strength;
    public $range;
    public $origStrength;
    public $attackStrength;
    public $defStrength;
    public $flankStrength;
    public $fireStrength;
    public $disorderedPlayerTurns = 0;
    public $bow;
    public $command = true;
    public $commandRadius = false;
    public $fireCombat = false;
    public $unitsBlock = true;
    public $isDisrupted = false;


    public function jsonSerialize()
    {
        if (is_object($this->hexagon)) {
            if ($this->hexagon->name) {
                $this->hexagon = $this->hexagon->getName();

            } else {
                $this->hexagon = $this->hexagon->parent;
            }
        }
        return $this;
    }


    public function getMaxMove(){
        $maxMove = parent::getMaxMove();
        if($this->isDisrupted !== false){
            return 0;
        }
        return $maxMove;
    }

    public function getUnmodifiedStrength(){
        return $this->defStrength;

        $b = Battle::getBattle();

         $strength = $this->attackStrength;

        return  $strength;
    }

    public function isBow(){
        return $this->bow === true;
    }

    public function __get($name)
    {

        $b = Battle::getBattle();
//        if ($name !== "strength") {
//            if($name == "flankStrength"){
//                return $this->flankStrength;
//            }
//
//            return false;
//        }
//        $strength = $this->getUnmodifiedSftrength();
//        if($name == "flankStrength"){
//            return $this->flankStrength;
//        }
        $strength = null;
        if($name === "attackStrength"){
            $strength = $this->attackStrength;
        }
        if($name === "fireStrength"){
            $strength = $this->fireStrength;
        }
        if($name === "defStrength"){
            $strength = $this->defStrength;
        }
        if($name === "flankStrength"){
            $strength = $this->flankStrength;
        }
//        if($name === "bowStrength"){
//            $strength = $this->attackStrength;
//        }
//
        foreach ($this->adjustments as $adjustment) {
            switch ($adjustment) {
                case 'floorHalf':
                    $strength = floor($strength / 2);
                    break;
                case 'half':
                    $strength = $strength / 2;
                    break;
                case 'double':
                    $strength = $strength * 2;
                    break;
            }
        }
        return $strength;
    }


    function set($unitName,
                  $unitForceId,
                  $unitHexagon,
                 $attackStrength,
                 $defStrength,
                 $flankStrength,
                  $range,
                  $unitMaxMove,
                  $unitStatus,
                  $unitReinforceZone,
                  $unitReinforceTurn,
                  $nationality,
                  $class,
                  $unitDesig,
                  $facing,
                 $armorClass,
                $bow,
                $fireStrength = false)
    {

        $this->dirty = true;
        $this->name = $unitName;
        $this->forceId = $unitForceId;
        $this->class = $class;

        $this->hexagon = new Hexagon($unitHexagon);
        $this->fireStrength = $fireStrength;
        $this->attackStrength = $attackStrength;
        $this->defStrength = $defStrength;
        $this->flankStrength = $flankStrength;


        $battle = Battle::getBattle();
        $mapData = $battle->mapData;

        $mapHex = $mapData->getHex($this->hexagon->getName());
        if ($mapHex) {
            $mapHex->setUnit($this->forceId, $this);
        }


        $this->maxMove = $unitMaxMove;
        $this->moveAmountUnused = $unitMaxMove;
        $this->status = $unitStatus;
        if($facing !== false){
            $this->facing = $facing;
        }
        $this->moveAmountUsed = 0;
        $this->reinforceZone = $unitReinforceZone;
        $this->reinforceTurn = $unitReinforceTurn;
        $this->combatNumber = 0;
        $this->combatIndex = 0;
        $this->combatOdds = "";
        $this->moveCount = 0;
        $this->retreatCountRequired = 0;
        $this->combatResults = NE;
        if($class === "hq"){
            $this->commandRadius = $range;
            $this->range = 1;
        }else{
            $this->range = $range;
        }
        $this->nationality = $nationality;
        $this->unitDesig = $unitDesig;
        $this->armorClass = $armorClass;
        $this->vp = 0;
        $this->bow = $bow;
        if(!$this->bow){
            $this->noZoc = true;
        }
    }


    function eliminate(){
    }

    public function disorderUnit(){
        $b = Battle::getBattle();
        $this->orgStatus = self::DISORDERED;
        $this->disorderedPlayerTurns = 2;
        $b->victory->disorderUnit($this);
    }

    public function checkLos(\Wargame\Los $los, $defenderId = false){
        if(!isset($this->facing)){
            return true;
        }
        $unitFacing = $this->facing;
        $attackFacing = ($los->getBearing() / 4);
        /*
         * non wrapping facing
         */
        if($unitFacing >= 1 && $unitFacing <= 4){
            return $attackFacing >= $unitFacing - 1 && $attackFacing <= $unitFacing + 1;
        }
        if($unitFacing === 0){
            return (($attackFacing >= 5 && $attackFacing <= 6) || ($attackFacing >= 0 && $attackFacing <= 1));
        }
        if($unitFacing === 5){
            return (($attackFacing >= 4 && $attackFacing <= 6) || ($attackFacing == 0));
        }
        return false;
    }

    public function rallyCheck(){
        if($this->orgStatus === self::DISORDERED){

            if($this->disorderedPlayerTurns === 0){
                $this->orgStatus = self::BATTLE_READY;
            }
            $this->disorderedPlayerTurns--;
        }
    }

    function damageUnit($kill = false)
    {
        $battle = Battle::getBattle();


        $this->status = STATUS_ELIMINATING;
        $this->exchangeAmount = $this->getUnmodifiedStrength();
        $this->defExchangeAmount = $this->getUnmodifiedStrength();
        $this->disorderedPlayerTurns = 0;
        $this->orgStatus = self::BATTLE_READY;
        $this->steps = $this->origSteps;

        return true;

    }

    function __construct($data = null)
    {
        if ($data) {
            foreach ($data as $k => $v) {
                if ($k == "hexagon") {
                    $this->hexagon = new Hexagon($v);
                    continue;
                }
                $this->$k = $v;
            }
            $this->dirty = false;
        } else {
            $this->adjustments = new stdClass();
        }
    }


    public function fetchData(){
        $mapUnit = new StdClass();
        $mapUnit->parent = $this->hexagon->parent;
        $mapUnit->moveAmountUsed = $this->moveAmountUsed;
        $mapUnit->maxMove = $this->getMaxMove();
        $mapUnit->strength = $this->getUnmodifiedStrength();
        $mapUnit->fireStrength = $this->fireStrength;
        $mapUnit->class = $this->class;
        $mapUnit->id = $this->id;
        $mapUnit->facing = $this->facing;
        $mapUnit->range = $this->range;
        $mapUnit->unitDefenseStrength = $this->unitDefenseStrength;
        $mapUnit->range = $this->range;
        $mapUnit->status = $this->status;
        $mapUnit->forceId = $this->forceId;
        $mapUnit->orgStatus = $this->orgStatus;
        $mapUnit->armorClass = $this->armorClass;
        $mapUnit->steps = $this->steps;
        $mapUnit->hexagon = $this->hexagon->name;
        $mapUnit->commandRadius = $this->commandRadius;
        $mapUnit->command = $this->command;
        $mapUnit->forceMarch = $this->forceMarch;
        $mapUnit->attackStrength = $this->attackStrength;
        $mapUnit->defstrength = $this->defStrength;
        $mapUnit->flankStrength = $this->flankStrength;
        $mapUnit->isDisrupted = $this->isDisrupted;
        return $mapUnit;
    }

    function disruptUnit($phase){
        $this->isDisrupted = $phase;
    }

    public function attemptUnDisrupt($phase){
        if($this->isDisrupted == $phase){
            $this->isDisrupted = false;
        }
    }
    function setStatus($status)
    {
        $battle = Battle::getBattle();
        $success = false;
        $prevStatus = $this->status;
        switch ($status) {
            case STATUS_EXCHANGED:
                if (($this->status == STATUS_CAN_DEFEND_LOSE || $this->status == STATUS_CAN_ATTACK_LOSE || $this->status == STATUS_CAN_EXCHANGE)) {
                    if($this->status === STATUS_CAN_DEFEND_LOSE && $battle->gameRules->mode !== DEFENDER_LOSING_MODE){
                        break;
                    }
                    if($this->status === STATUS_CAN_ATTACK_LOSE && $battle->gameRules->mode !== ATTACKER_LOSING_MODE){
                        break;
                    }
                    $this->damageUnit();
                    $success = true;
                }
                break;

            case STATUS_REPLACING:
                if ($this->status == STATUS_CAN_REPLACE) {
                    $this->status = $status;
                    $success = true;
                }
                break;

            case STATUS_REPLACED:
                if ($this->status == STATUS_REPLACING) {
                    $this->status = $status;
                    $success = true;
                }
                break;

            case STATUS_CAN_REPLACE:
                if ($this->status == STATUS_REPLACING) {
                    $this->status = $status;
                    $success = true;
                }
                break;

            case STATUS_REINFORCING:
                if ($this->status == STATUS_CAN_REINFORCE) {
                    $this->status = $status;
                    $success = true;
                }
                break;

            case STATUS_DEPLOYING:
                if ($this->status == STATUS_CAN_DEPLOY) {
                    $this->status = $status;
                    $success = true;
                }
                break;

            case STATUS_CAN_REINFORCE:
                if ($this->status == STATUS_REINFORCING) {
                    $this->status = $status;
                    $success = true;
                }
                break;

            case STATUS_CAN_DEPLOY:
                if ($this->status == STATUS_DEPLOYING) {
                    $this->status = $status;
                    $success = true;
                }
                break;

            case STATUS_READY:
            case STATUS_DEFENDING:
            case STATUS_ATTACKING:
                $this->status = $status;
                $id = $this->id;
                if ($status === STATUS_ATTACKING) {
                    if ($battle->force->combatRequired && isset($battle->force->requiredAttacks->$id)) {
                        $battle->force->requiredAttacks->$id = false;
                    }
                }
                if ($status === STATUS_DEFENDING) {
                    if ($battle->force->combatRequired && isset($battle->force->requiredDefenses->$id)) {
                        $battle->force->requiredDefenses->$id = false;
                    }
                }
                if ($status === STATUS_READY) {

                    if ($battle->force->combatRequired && isset($battle->force->requiredAttacks->$id)) {
                        $battle->force->requiredAttacks->$id = true;
                    }
                    if ($battle->force->combatRequired && isset($battle->force->requiredDefenses->$id)) {
                        $battle->force->requiredDefenses->$id = true;
                    }
                }
                break;

            case STATUS_MOVING:
                if (($this->status == STATUS_READY || $this->status == STATUS_REINFORCING)
                ) {
                    $this->status = $status;
                    $this->moveCount = 0;
                    $this->moveAmountUsed = 0;
                    $this->moveAmountUnused = $this->getMaxMove();
                    $success = true;
                }
                break;

            case STATUS_STOPPED:
                if ($this->status == STATUS_MOVING || $this->status == STATUS_DEPLOYING) {
                    $this->status = $status;
                    $this->moveAmountUnused = $this->getMaxMove() - $this->moveAmountUsed;
                    $this->moveAmountUsed = $this->getMaxMove();

                    $success = true;
                }
                if ($this->status == STATUS_ADVANCING) {
                    $this->status = STATUS_ADVANCED;
//                    $this->moveAmountUsed = $$this->maxMove;
                    $success = true;
                }
                if ($this->status == STATUS_RETREATING) {
                    $this->status = STATUS_RETREATED;
//                    $this->moveAmountUsed = $$this->maxMove;
                    $success = true;
                }
                break;

            case STATUS_EXITED:
                if ($this->status == STATUS_MOVING) {
                    $this->status = $status;
                    $success = true;
                }
                break;

            case STATUS_RETREATING:
                if ($this->status == STATUS_CAN_RETREAT) {
                    $this->status = $status;
                    $this->moveCount = 0;
                    $this->moveAmountUsed = 0;
                    $success = true;
                }
                break;

            case STATUS_ADVANCING:
                if ($this->status == STATUS_CAN_ADVANCE || $this->status == STATUS_MUST_ADVANCE) {
                    $this->status = $status;
                    $this->moveCount = 0;
                    $this->moveAmountUsed = 0;
                    $success = true;
                }
                break;

            case STATUS_ADVANCED:
                if ($this->status == STATUS_ADVANCING) {
                    $this->status = $status;
                    $success = true;
                }
                break;

            default:
                break;
        }
        $this->dirty = true;
        return $success;
    }

    public function getRange(){
        return $this->range;
    }

    public function usedFireCombat(){
        return $this->fireCombat;
    }

    public function setFireCombat(){
        $this->fireCombat = true;
    }

    public function clearFireCombat(){
        $this->fireCombat = false;
    }
    /* 999999999 */
    public function getZocNeighbors($neighbors){
        if($this->noZoc === true){
            return [];
        }
        if(isset($this->facing)){
            $neighbors = array_slice(array_merge($neighbors,$neighbors), ($this->facing + 6 - 1)%6, 3);
        }
        return $neighbors;
    }

}