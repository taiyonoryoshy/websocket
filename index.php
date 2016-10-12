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
  var ws = new WebSocket('ws://websocket:8000');

  function wsStart() {
    ws.open = function () {
      console.log('open');
    };

    ws.onclose = function () {
      console.log('ws close!');
      setTimeout(wsStart, 1000);
    };

    ws.onmessage = function (e) {
      var response = JSON.parse(e.data);

      clickX = response[0].concat(clickX);
      clickY = response[1].concat(clickY);
      clickDrag = response[2].concat(clickDrag);
      redraw(clickX, clickY, clickDrag);
    };
  }

  wsStart();

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
    var mouseX = e.pageX - this.offsetLeft;
    var mouseY = e.pageY - this.offsetTop;

    paint = true;
    addClick(e.pageX - this.offsetLeft, e.pageY - this.offsetTop);
    redraw(clickX, clickY, clickDrag);
  });

  canvas.addEventListener('mousemove', function (e) {
    if (paint) {
      addClick(e.pageX - this.offsetLeft, e.pageY - this.offsetTop, true);
      redraw(clickX, clickY, clickDrag);
    }
    e.preventDefault();
  });

  canvas.addEventListener('mouseup', function (e) {
    paint = false;

    ws.send(JSON.stringify([clickX, clickY, clickDrag]));
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

  function redraw(clickX, clickY, clickDrag) {
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