/**
 * Created by matthew on 2/15/2016.
 */
var ajax = new XMLHttpRequest();
var myArr;
var gaugesMade;
var gauges = new Array();

function getData(classtoGet) {
    ajax.onreadystatechange = bob;
    ajax.open('GET', 'data/data.php?classid=' + classtoGet, true);
    ajax.send();
}
function bob() {
    if (ajax.readyState === 4) {
        myArr = JSON.parse(ajax.responseText);
        if(!gaugesMade){
            makeGauges(myArr);
        }else{
            updateGauges(myArr);
        }
    }
}
function hideDiv(myEl) {
    var tog = window.getComputedStyle(myEl).getPropertyValue('position');
    if (tog == 'absolute') {
        myEl.style.height = 'auto';
        myEl.style.opacity = '1';
        myEl.style.position = 'relative';
    } else {
        myEl.style.height = '0';
        myEl.style.opacity = '0';
        myEl.style.position = 'absolute';
    }
}

var config1 = liquidFillGaugeDefaultSettings();
config1.circleThickness = 0.15;
config1.circleColor = "#FFBF00";
config1.textColor = "#555500";
config1.waveTextColor = "#FFAAAA";
config1.waveColor = "#0000FF";
config1.textVertPosition = 0.8;
config1.waveAnimateTime = 1000;
config1.waveHeight = 0.05;
config1.waveRise = true;
config1.waveHeightScaling = false;
config1.waveAnimate = true;
config1.waveCount = 2;
config1.waveOffset = 0.15;
config1.textSize = 1;
config1.minValue = 0;
config1.maxValue = 100;
config1.displayPercent = true;

function makeGauges(gaugeArr) {
    gaugesMade=true;
    for (var i = 1; i <= gaugeArr.length; i++) {
        var myObj = gaugeArr[i - 1];
        var score = Math.round((parseInt(myObj.count) / parseInt(myObj.numstuds)) * 100);
        var title = myObj.test;
        if (isNaN(score)) score = 0;
        var gauge = loadLiquidFillGauge("fillgauge" + i, score, config1);
        document.getElementById("fillgauge" + i).nextElementSibling.childNodes[0].nodeValue = title;
        gauges.push(gauge);
    }
}

function updateGauges(gaugeArr){
    for (var i = 1; i <= gaugeArr.length; i++) {
        var myObj = gaugeArr[i - 1];
        var score = Math.round((parseInt(myObj.count) / parseInt(myObj.numstuds)) * 100);
        if (isNaN(score)) score = 0;
        gauges[i-1].update(score);
    }
}