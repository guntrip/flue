<head>
<style>
body {
  text-align: center;
  font-family: Arial, Helvetica, sans-serif;
  background-color:#5f5f5f;
  color:#fff;
}
.box {
  display:inline-block;
  width:40%;
  padding: 0px 40px;
  text-align:center;
  margin-bottom:20px;
  vertical-align: top;
}
.box button {
  font-size:1.6em;
  font-weight:bold;
  margin:10px;
  background-color: #000;
  color:#fff;
}
</style>
</head>

<center>
<h1>🐉🐉🐉🐉 fluebot d&d 🐉🐉🐉🐉</h1>
</center>

<div class="box">
  <h2>Actions</h2>
<button onclick="spell('fire', 'https://www.youtube.com/embed/8pKHniiwoJQ')">Fire</button>
<button onclick="spell('lightning', 'https://www.youtube.com/embed/ckzvf4GHcVM')">Lightning</button>
<button onclick="spell('ice', 'https://www.youtube.com/embed/Pp8NKtmmU4E')">Ice</button>
<button onclick="spell('magic', 'https://www.youtube.com/embed/V159_qFBxYs')">Magic</button>
</div>

<div class="box">
  <h2>Scenes (<label for="scenemusic"><input type="checkbox" id="scenemusic" checked> Music</label>)</h2>
<button onclick="scene('cave', 'https://www.youtube.com/embed/kxqJuc1HHbg')">Cave</button>
<button onclick="scene('cave_fire', 'https://www.youtube.com/embed/SKaIkbgyhU8')">Cave w/fire</button>
<button onclick="scene('cave_lit', 'https://www.youtube.com/embed/kxqJuc1HHbg')">Cave w/lit</button>
<button onclick="scene('outdoors_day', '')">Day</button>
<button onclick="scene('outdoors_night', 'https://www.youtube.com/embed/W8tVwiYsgHg')">Night</button>
<button onclick="scene('green', 'https://www.youtube.com/embed/e-4p9cDV6t8')">Green</button>
<button onclick="src('dnd', 'dnd.php?clear=1')">Clear scene</button>
</div>

<script>
function src(id, url) {
  document.getElementById(id).src = url;
}

function spell(dnd, fx) {
  src('fx', 'dnd_youtube.php?url='+fx);
  setTimeout(function(){ // delay for youtube!
    src('dnd', 'dnd.php?spell='+dnd);
  }, 750);

}

function scene(dnd, music) {
  src('dnd', 'dnd.php?scene='+dnd);
  if (document.getElementById("scenemusic").checked) {
    src('music', 'dnd_youtube.php?url='+music);
  }
}

// special stuff
function lightning() {
  src('dnd', 'dnd.php?spell=lightning');
  src('fx', 'https://www.youtube.com/embed/ckzvf4GHcVM?autoplay=1');
}

</script>
<iframe src="dnd.php" style="width:30%;height:400px;" id="dnd"></iframe>
<iframe src="" style="width:30%;height:400px;" id="fx">fx</iframe>
<iframe src="" style="width:30%;height:400px;" id="music">music</iframe>
