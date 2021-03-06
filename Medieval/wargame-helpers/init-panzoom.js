/**
 * Created by david on 2/19/17.
 */
/**
 * Created by PhpStorm.
 * User: david
 * Date: 2/19/17
 * Time: 3:21 PM

 /*
 * Copyright 2012-2017 David Rodal

 * This program is free software; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation;
 * either version 2 of the License, or (at your option) any later version

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
import "jquery-ui-bundle";
import "./imported/jquery.panzoom";
import {counterClick} from "@markarian/wargame-helpers";
import {DR} from '@markarian/wargame-helpers'
document.addEventListener("DOMContentLoaded",function(){

    DR.$ = $;
    DR.globalZoom = 1;
    DR.playerNameMap = ["Zero", "One", "Two", "Three", "Four"];

    DR.players = ["observer", DR.playerOne, DR.playerTwo, DR.playerThree, DR.playerFour];
    DR.crtDetails = false;
    DR.showArrows = false;


    var $panzoom = $('#gameContainer').panzoom({
        cursor: "normal", animate: true, maxScale: 2.0, minScale: .3,
        onPan: function (e, panzoom, e2, e3, e4) {

        },
        onZoom: function (e, p, q) {
            DR.globalZoom = q;
            DR.doingZoom = true;
            var out = DR.globalZoom.toFixed(1);

            $("#zoom .defaultZoom").html(out);
        },
        onEnd: function(a,b,c,d,e){

            let top = $(a.target).parents('.unit');
            let id = null;
            if(top.length){
                id = top.attr('id');
            }

            let clientX = a.clientX;
            let clientY = a.clientY;
            if(a.originalEvent.changedTouches) {
                clientX = a.originalEvent.changedTouches[0].clientX;
                clientY = a.originalEvent.changedTouches[0].clientY;
            }

            var xDrag = Math.abs(clientX - DR.clickX);
            var yDrag = Math.abs(clientY - DR.clickY);

            if (xDrag > 4 || yDrag > 4) {
                DR.dragged = true;
            }else{
                if(DR.doingZoom !== true && a.originalEvent.changedTouches && id !== null){
                    DR.$scope.clickMe(id, a);
                }
            }

            DR.doingZoom = false;
        },
        onStart: function(a,b,c,d,e){

            DR.doingZoom = false;

            DR.dragged = false;
            if(c.changedTouches){
                DR.clickX = c.changedTouches[0].clientX;
                DR.clickY = c.changedTouches[0].clientY;
            }else{
                DR.clickX = c.clientX;
                DR.clickY = c.clientY;
            }
        }
    });

    $panzoom.parent().on('mousewheel DOMMouseScroll MozMousePixelScroll', function (e) {
        e.preventDefault();
        var delta = e.delta || e.originalEvent.wheelDelta;

        var zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;

        $panzoom.panzoom('zoom', zoomOut, {
            increment: 0.1,
            animate: false,
            focal: e
        });
    });

    DR.$panzoom = $panzoom;

    /* Sync object, well named as x don't start fetching till everything is ready.*/


});

