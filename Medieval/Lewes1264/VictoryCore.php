<?php
namespace Wargame\Medieval\Lewes1264;
use Wargame\Battle;
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
/**
 * Created by JetBrains PhpStorm.
 * User: markarianr
 * Date: 5/7/13
 * Time: 7:06 PM
 * To change this template use File | Settings | File Templates.
 */
//include_once "victoryCore.php";

class VictoryCore extends \Wargame\Medieval\victoryCore
{

    protected $outgoingVP;

    function __construct($data)
    {
        parent::__construct($data);
        if ($data) {

        } else {

        }
        $this->outgoingVP = [0,0,0,0,0];
    }

    public function setSupplyLen($supplyLen)
    {
        $this->supplyLen = $supplyLen[0];
    }

    public function save()
    {
        $ret = parent::save();
        return $ret;
    }

    public function specialHexChange($args)
    {
        $battle = Battle::getBattle();

        list($mapHexName, $forceId) = $args;
        $vp = 10;

        /*
         *  Teutonic Camp
         */

        $pData = $battle->getPlayerData($battle->scenario);
        $class = preg_replace("/ /", "-",$pData['forceName'][$forceId]);
        if(in_array($mapHexName,$battle->specialHexB)) {
            if ($forceId == Lewes1264::LOYALIST_FORCE) {
                $this->victoryPoints[Lewes1264::LOYALIST_FORCE] += $vp;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='$class'> +$vp Loyalist vp</span>";
            }
            if ($forceId == Lewes1264::REBEL_FORCE) {
                $this->victoryPoints[Lewes1264::LOYALIST_FORCE] -= $vp;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='$class'> -$vp Loyalist vp</span>";
            }
        }

        /*
         * Polish Camp
         */
        if(in_array($mapHexName,$battle->specialHexA)){

            if ($forceId == Lewes1264::LOYALIST_FORCE) {
                $this->victoryPoints[Lewes1264::REBEL_FORCE]  -= $vp;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='$class'>-$vp Rebel vp</span>";
            }
            if ($forceId == Lewes1264::REBEL_FORCE) {
                $this->victoryPoints[Lewes1264::REBEL_FORCE]  += $vp;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='$class'>+$vp Rebel vp</span>";
            }
        }

    }
    
    public function gameEnded()
    {
        $battle = Battle::getBattle();
        if ($this->victoryPoints[Lewes1264::REBEL_FORCE] > $this->victoryPoints[Lewes1264::LOYALIST_FORCE]) {
            $battle->gameRules->flashMessages[] = "Rebel Player Wins";
            $this->winner = Lewes1264::REBEL_FORCE;
        }
        if ($this->victoryPoints[Lewes1264::LOYALIST_FORCE] > $this->victoryPoints[Lewes1264::REBEL_FORCE]) {
            $battle->gameRules->flashMessages[] = "Loyalist Player Wins";
            $this->winner = Lewes1264::LOYALIST_FORCE;
        }
        if ($this->victoryPoints[Lewes1264::REBEL_FORCE] == $this->victoryPoints[Lewes1264::LOYALIST_FORCE]) {
            $battle->gameRules->flashMessages[] = "Tie Game";
        }
        $this->gameOver = true;
        return true;
    }

}