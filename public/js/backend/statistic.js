/**
 * Created by Jasmin on 05.01.2017.
 */

var Statistic = function () {
    // document.getElementById('statisticList').style.display = 'none';
    var elements = document.getElementsByClassName('jsStatistic');
    for (var i = 0; i < elements.length; i++) {
        elements[i].style.display = 'block';
    }


    this.initCanvas();

};

Statistic.prototype.initCanvas = function () {
    this.canvas = document.getElementById('statisticCanvas');
    this.context = this.canvas.getContext('2d');
};

Statistic.prototype.drawCircle = function (circleData) {

    for (var i = 0; i < Object.keys(circleData).length; i++) {
        this.drawSegment(circleData, i);
    }
};

Statistic.prototype.drawSegment = function (circleData, i) {
    this.context.save();
    var centerX = Math.floor(this.canvas.width / 2);
    var centerY = Math.floor(this.canvas.height / 2);
    var radius = Math.floor(this.canvas.width / 2);

    var startingAngle = this.degreesToRadians(this.sumTo(circleData, i));
    var arcSize = this.degreesToRadians(circleData[i]['value']);
    var endingAngle = startingAngle + arcSize;

    this.context.beginPath();
    this.context.moveTo(centerX, centerY);
    this.context.arc(centerX, centerY, radius, startingAngle, endingAngle, false);
    this.context.closePath();

    this.context.fillStyle = circleData[i]['color'];
    this.context.fill();

    this.context.restore();

    this.drawSegmentLabel(circleData, i);
};


Statistic.prototype.drawSegmentLabel = function (circleData, i) {
    this.context.save();
    var x = Math.floor(this.canvas.width / 2);
    var y = Math.floor(this.canvas.height / 2);
    var angle = this.degreesToRadians(this.sumTo(circleData, i));
    var test = -10;
    var testY = 0;
    if (angle > Math.PI / 2) {
        angle -= Math.PI;
        test = -this.canvas.width + 70;
        testY = -20;
    }

    this.context.translate(x, y);
    this.context.rotate(angle);
    var dx = Math.floor(this.canvas.width * 0.5) + test;
    var dy = Math.floor(this.canvas.height * 0.05) + testY;

    this.context.textAlign = "right";
    var fontSize = Math.floor(this.canvas.height / 25);
    this.context.font = fontSize + "pt Helvetica";

    this.context.fillText(circleData[i]['label'], dx, dy);

    this.context.restore();
};


Statistic.prototype.degreesToRadians = function (degrees) {
    return (degrees * Math.PI) / 180;
};

Statistic.prototype.sumTo = function (a, i) {
    var sum = 0;
    for (var j = 0; j < i; j++) {
        sum += a[j]['value'];
    }
    return sum;
};


