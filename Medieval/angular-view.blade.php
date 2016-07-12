<?php
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
?>
<script src="<?=url("js/angular.js");?>"></script>
<script src="<?=url("js/ng-right-click.js");?>"></script>

@section('units')
    <div class="a-unit-wrapper" ng-repeat="unit in mapUnits"  ng-style="unit.wrapperstyle">
        <unit unit="unit"></unit>
    </div>

    <div ng-mouseover="hoverThis(unit)" ng-mouseleave="unHoverThis(unit)" ng-click="clickMe(unit.id, $event)" ng-style="unit.style" ng-repeat="unit in moveUnits track by $index" class="unit" ng-class="[unit.nationality, unit.class]" >
        <ghost-unit unit="unit"></ghost-unit>

    </div>
@endsection
@section('outer-deploy-box')
    <div style="margin-right:3px;" class="left">Deploy/Staging area</div>
    <div id="deployBox">
        <div class="a-unit-wrapper" ng-repeat="unit in deployUnits"  ng-style="unit.wrapperstyle">
             <offmap-unit unit="unit"></offmap-unit>
        </div>
        <div class="clear"></div>
    </div>
@endsection



<body ng-app="lobbyApp" xmlns="http://www.w3.org/1999/html">
<div ng-controller="LobbyController" id="theDiv">
    <header id="header">
        <div id="headerContent">
            <div id="mouseMove">mouse</div>

            <div class="dropDown alpha" id="menuWrapper">
                <h4 class="WrapperLabel" title="Game Menu"><i class="tablet fa fa-bars"></i><span class="desktop">Menu</span></h4>

                <div id="menu">
                    <div class="close">X</div>
                    <ul>
                        @section('inner-menu')
                        <li><a id="muteButton">mute</a></li>
                        <li><a href="<?= url("wargame/leave-game"); ?>">Go To Lobby</a></li>
                        <li><a href="<?= url("logout"); ?>">logout</a></li>
                        <li><a id="arrowButton">show arrows</a></li>
                        <li><a href="#" onclick="seeUnits();return false;">See Units</a></li>
                        <li><a href="#" onclick="seeBoth();return false;">See Both</a></li>
                        <li><a href="#" onclick="seeMap();return false;">See Map</a></li>
                        @show
                        <li class="closer"></li>
                    </ul>
                </div>
            </div>
            <div class="dropDown" id="infoWrapper">
                <h4 class="WrapperLabel" title="Game Information"><i class="tablet">i</i><span class="desktop">Info</span></h4>
                <div id="info">
                    <div class="close">X</div>
                    <ul>
                        <li> Welcome {{$user}}</li>
                        <li>you are playing as  <?= $player; ?></li>
                        <li>
                            in <span class="game-name">{{$gameName}}-{{$arg}}</span></li>
                        <li> The file is called {{$name}}</li>
                        <!-- TODO: make game credits from DB -->
                        <li>Game Designer: David Rodal</li>
                        <li class="closer"></li>
                    </ul>
                </div>
            </div>
            <div id="crtWrapper">
                <h4 class="WrapperLabel" title='Combat Results Table'>
                    <span>CRT</span></h4>

                <div id="crt">
                    <div class="close">X</div>
                    <h3>Combat Odds</h3>

                    @section('inner-crt')
                        @include('wargame::stdIncludes.inner-crt',['topCrt'=> new \Wargame\TMCW\CombatResultsTable()])
                    @show

                    <div ng-click="toggleDetails()" id="angCrtDetailsButton">details</div>
                </div>
            </div>
            @include("wargame::stdIncludes.timeTravel")
            <?php //include "timeTravel.php"; ?>
            <div id="statusWrapper">
                <div id="comlinkWrapper">
                    <div id="comlink"></div>
                </div>
                <div id="topStatus"></div>
                <div class="clear">
                    <span id="status"></span>
                    <span id="victory"></span>
                </div>
            </div>
            <div id="zoomWrapper">
                    <span id="zoom">
                        <span class="defaultZoom">1.0</span>
                    </span>
            </div>
            <div class="dropDown">
                <h4 class="WrapperLabel"><span class="tablet">?</span><span class="desktop">Rules</span></h4>
                <div class="subMenu">

                    @section('commonRules')
                    @show
                    @section('exclusiveRules')
                    @show
                    @section('obc')
                    @show
                </div>
            </div>
            <?php //include_once "tec.php"; ?>

            @section('outer-units-menu')
            <div class="dropDown" id="unitsWrapper">
                <h4 class="WrapperLabel" title="Offmap Units">Units</h4>

                <div id="units" class="subMenu">
                        <div class="dropDown" id="closeAllUnits">Close All</div>
                        <div class="dropDown" id="hideShow">Retired Units</div>
                        <div class="dropDown" id="showDeploy">Deploy/Staging Box</div>
                        <div class="dropDown" id="showExited">Exited Units</div>
                        <div class="dropDown" id="showNotUsed">Not Used</div>

                </div>
            </div>
            @show
            @section('outer-aux-menu')
            @show

            <div id="nextPhaseWrapper">
                <button id="nextPhaseButton">Next Phase</button>
                <button id="fullScreenButton"><i class="fa fa-arrows-alt"></i></button>
                <button class="dynamicButton combatButton" id="determinedAttackEvent">d</button>
                <button class="dynamicButton movementButton" id="forceMarchEvent">m</button>
                <button class="dynamicButton combatButton" id="clearCombatEvent">c</button>
                <button class="dynamicButton combatButton" id="shiftKey">+</button>

            </div>

            <div style="clear:both;"></div>

        </div>
    </header>
    <div id="content">
        <div id="rightCol">
            @section('unit-boxes')
            <div class="unit-wrapper" id="deployWrapper">
                <div class="close">X</div>
                @section('outer-deploy-box')
                <div id="deployBox"></div>
                @show
                <div style="clear:both;"></div>
            </div>

            <div class="unit-wrapper" style="display:none;"  id="deadpile">
                <div class="close">X</div>
                <div style="right:10px;font-size:50px;font-family:sans-serif;bottom:10px;position:absolute;color:#666;">
                    Retired Units
                </div>
                <div class="a-unit-wrapper" ng-repeat="unit in retiredUnits"  ng-style="unit.wrapperstyle">
                    <offmap-unit unit="unit"></offmap-unit>
                </div>
                <div class="clear"></div>
            </div>
            <div class="unit-wrapper" style="display:none;" id="exitWrapper">
                <div class="close">X</div>
                <div style="margin-right:3px;" class="left">Exited Units</div>
                <div id="exitBox">
                </div>
                <div style="clear:both;"></div>
            </div>
            <div class="unit-wrapper" style="display:none;" id="notUsedWrapper">
                <div class="close">X</div>
                <div style="margin-right:3px;" class="left">Units not used.</div>
                <div class="a-unit-wrapper" ng-repeat="unit in notUsedUnits"  ng-style="unit.wrapperstyle">
                    <offmap-unit unit="unit"></offmap-unit>
                </div>
                <div class="clear"></div>
            </div>

            <div class="unit-wrapper" style="display:none;" id="undeadpile"></div>
            @show
            <div id="gameViewer">
                <div id="gameContainer">
                    <div id="gameImages">
                        @section('game-images')
                        <div id="svgWrapper">
                            <svg id="arrow-svg" style="opacity:.6;position:absolute;" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <marker id='heead' orient="auto"
                                            markerWidth='2' markerHeight='4'
                                            refX='0.1' refY='2'>
                                        <!-- triangle pointing right (+x) -->
                                        <path d='M0,0 V4 L2,2 Z' />
                                    </marker>
                                    <marker
                                        inkscape:stockid="Arrow1Lend"
                                        orient="auto"
                                        refY="0.0"
                                        refX="0.0"
                                        id="head"
                                        style="overflow:visible;">
                                        <path
                                            id="path3762"
                                            d="M 0.0,0.0 L 5.0,-5.0 L -12.5,0.0 L 5.0,5.0 L 0.0,0.0 z "
                                            style="fill-rule:evenodd;stroke:#000000;stroke-width:1.0pt;"
                                            transform="scale(0.15) rotate(180) translate(12.5,0)" />
                                    </marker>
                                </defs>
                            </svg>
                        </div>
                        <img id="map" alt="map" src="<?php preg_match("/http/",$mapUrl) ?   $pre = '': $pre = url('.');echo "$pre$mapUrl";?>">
                        <?php $id = 0; ?>

                        @section('units')
                            {{-- Handled by angular above and below --}}
                        @show

                        <div ng-style="{top: floatMessage.top, left: floatMessage.left}" ng-show="floatMessage.header" id="floatMessage">
                            <header>@{{ floatMessage.header }}</header>
                            <p>@{{ floatMessage.body }}</p>
                        </div>
                    </div>
                    @show
                </div>
            </div>

            <audio class="pop" src="{{asset('js/pop.m4a')}}"></audio>
            <audio class="poop" src="{{asset('js/lowpop.m4a')}}"></audio>
            <audio class="buzz" src="{{asset('js/buzz.m4a')}}"></audio>

        </div>
    </div>
    <div id="display"></div>
</div>

@include('wargame::Medieval.medieval-controller', ['topCrt'=> $topCrt])

</body>

<script type="text/ng-template" id="unit.html" >
    <div id="@{{unit.id}}" ng-mouseDown="mouseDown(unit.id, $event)" ng-mouseUp="clickMe(unit.id, $event)" ng-right-click="rightClickMe(unit.id, $event)"  ng-style="unit.style" class="unit rel-unit" ng-class="[unit.nationality, unit.class]" >
        <div ng-show="unit.oddsDisp" class="unitOdds" ng-class="unit.oddsColor">@{{ unit.oddsDisp }}</div>
        <div class="shadow-mask" ng-class="unit.shadow"></div>
        <div class="counterWrapper">
            <div ng-show="unit.bow" class="bow" style=""></div>
            <div ng-show="unit.hq" class="hq">@{{ unit.commandRadius }}</div>
            <div class="counter"></div>
        </div>
        <div class="range">@{{ unit.armorClass }}</div>

        <img ng-repeat="arrow in unit.arrows" ng-style="arrow.style" class="arrow" src="{{asset('js/short-red-arrow-md.png')}}" class="counter">

        <div ng-class="unit.infoLen" class="unit-numbers">@{{ unit.unitNumbers }}</div>
        <div class="unit-steps">@{{ "...".slice(0, unit.steps) }}</div>
        <i ng-show="!unit.command" class="fa fa-star unit-command" aria-hidden="true"></i>

    </div>
</script>

<script type="text/ng-template" id="offmap-unit.html" >
    <div id="@{{unit.id}}" ng-mouseUp="clickMe(unit.id, $event)" ng-style="unit.style" class="unit rel-unit" ng-class="[unit.nationality, unit.class]" >
        <div ng-show="unit.oddsDisp" class="unitOdds" ng-class="unit.oddsColor">@{{ unit.oddsDisp }}</div>
        <div class="shadow-mask" ng-class="unit.shadow"></div>
        <div class="counterWrapper">
            <div ng-show="unit.bow" class="bow" style=""></div>
            <div ng-show="unit.hq" class="hq">@{{ unit.commandRadius }}</div>

            <div class="counter"></div>
        </div>
        <div class="range">@{{ unit.armorClass }}</div>

        <img ng-repeat="arrow in unit.arrows" ng-style="arrow.style" class="arrow" src="{{asset('js/short-red-arrow-md.png')}}" class="counter">

        <div class="unit-numbers">@{{ unit.strength }} @{{ unit.orgStatus == 0 ? 'B':'D' }} @{{ unit.maxMove - unit.moveAmountUsed }}</div>
        <div class="unit-steps">@{{ "...".slice(0, unit.steps) }}</div>

    </div>
</script>

<script type="text/ng-template" id="ghost-unit.html" >
<div class="counterWrapper">
    <div ng-show="unit.bow" class="bow" style=""></div>
    <div ng-show="unit.hq" class="hq">@{{ unit.commandRadius }}</div>
    <div class="counter"></div>
</div>
<div class="range">@{{ unit.armorClass }}</div>
<div class="unit-numbers">@{{ unit.strength }} - @{{ unit.pointsLeft }}</div>
<div class="unit-steps">@{{ "...".slice(0, unit.steps) }}</div>
</script>

</html>
