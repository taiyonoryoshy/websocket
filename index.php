<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Document</title>
</head>
<body>

<h1>Paint here!</h1>

<div id="canvasDiv"></div>

<script>
  var canvasDiv = document.getElementById('canvasDiv');
  canvas = document.createElement('canvas');
  canvas.setAttribute('width', 1600);
  canvas.setAttribute('height', 800);
  canvas.setAttribute('id', 'canvas');
  canvas.style.border = '2px solid #000';
  canvasDiv.appendChild(canvas);
  if (typeof G_vmlCanvasManager != 'undefined') {
    canvas = G_vmlCanvasManager.initElement(canvas);
  }
  context = canvas.getContext("2d");

  canvas.addEventListener('mousedown', function (e) {
    console.log('mousedown');
    var mouseX = e.pageX - this.offsetLeft;
    var mouseY = e.pageY - this.offsetTop;

    paint = true;
    addClick(e.pageX - this.offsetLeft, e.pageY - this.offsetTop);
    redraw();
  });

  canvas.addEventListener('mousemove', function (e) {
    if (paint) {
      addClick(e.pageX - this.offsetLeft, e.pageY - this.offsetTop, true);
      redraw();
    }
    e.preventDefault();
  });

  canvas.addEventListener('mouseup', function (e) {
    paint = false;
  });

  canvas.addEventListener('mouseleave', function (e) {
    paint = false;
  });

  var clickX = [];
  var clickY = [];
  var clickDrag = [];
  var paint;

  function addClick(x, y, dragging) {
    clickX.push(x);
    clickY.push(y);
    clickDrag.push(dragging);
  }

  function redraw() {
    context.clearRect(0, 0, context.canvas.width, context.canvas.height); // Clears the canvas

    context.strokeStyle = "#df4b26";
    context.lineJoin = "round";
    context.lineWidth = 5;

    for (var i = 0; i < clickX.length; i++) {
      context.beginPath();
      if (clickDrag[i] && i) {
        context.moveTo(clickX[i - 1], clickY[i - 1]);
      } else {
        context.moveTo(clickX[i] - 1, clickY[i]);
      }
      context.lineTo(clickX[i], clickY[i]);
      context.closePath();
      context.stroke();
    }
  }

</script>
</body>
</html>