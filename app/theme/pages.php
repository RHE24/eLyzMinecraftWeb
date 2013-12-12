<?php 
if(!empty($_GET["page"])) $page = mysql_real_escape_string(trim($_GET["page"])); else $page = "news";
$radku = mysql_num_rows(mysql_query("SELECT `id` FROM `" . $page . "`"));
$po = 6;
$max_stranek = ceil($radku / $po);
if(!empty($_GET["stranka"])) $url_stranka = ($_GET["stranka"] / $po) + 1; else $url_stranka = (0 / $po) + 1;
if(empty($_GET["stranka"])) { $stranka = 0; } else { $stranka = $_GET["stranka"]; }
if(!empty($_GET["stranka"])){
	$cist = mysql_query("SELECT * FROM `" . $page . "` ORDER BY id DESC LIMIT " . intval(mysql_real_escape_string($stranka)) . "," . $po);
	while($rownews = mysql_fetch_assoc($cist)) {
		?>
	<div class="poster">
     <div class="leftside">
      <div class="inline-byline">
       <span>Autor:</span>
       <span class="inline-byline-author">
        <a href="#" title="" rel="author"><?php echo $rownews["user"]; ?></a>
       </span>
       <span class="comment">
        <a href="#" title="">0</a>
       </span>
      </div>
     </div>
     <div class="post postcontent">
      <h2 class="entry-title">
       <a href="#" title=""><?php echo $rownews["name"]; ?></a>
      </h2>
      <?php echo $rownews["name"]; ?>
     </div>
     <div class="clear"></div>
    </div>
		<?php
	}
} else {
	$cist = mysql_query("SELECT * FROM `" . $page . "` ORDER BY id DESC LIMIT " . $po);
	if(empty($cist)) echo file_get_contents("./app/theme/404.latte");
	while($rownews = mysql_fetch_assoc($cist)) {
		?>
	<div class="poster">
     <div class="leftside">
      <div class="inline-byline">
       <?php if(!empty($rownews["user"])): ?>
       <span>Autor:</span>
       <span class="inline-byline-author">
        <a href="#" title="" rel="author"><?php echo $rownews["user"]; ?></a>
       </span>
       <?php endif; ?>
       <span class="comment">
        <a href="#" title="">0</a>
       </span>
      </div>
     </div>
     <div class="post postcontent">
      <?php if(!empty($rownews["name"])): ?>
      <h2 class="entry-title">
       <a href="#" title=""><?php echo $rownews["name"]; ?></a>
      </h2>
      <?php endif; ?>
      <?php echo $rownews["content"]; ?>
     </div>
     <div class="clear"></div>
    </div>
		<?php
	}
}
if(empty($_GET["page"])) {
	echo "<center><h3>";
	for($i=0; $i < $max_stranek; $i++) {
		$cislo = ($i + 1);
		$url_cislo  = ($cislo * $po) - $po;
		if($url_stranka != $cislo) {
			echo "<strong><a href=\"?stranka=" . $url_cislo . "\">" . ($i + 1) . "</a></strong>\n";
		} else {
			echo "<strong>".($i + 1)."</strong>\n";
		}
	}
	echo "</h3></center><br>";
}
?>