<?php
	//Einstellungen zur Datenbankverbindung
	$port 		= 5432;
	$host 		= "localhost";
	$datenbank	= "election";
	$user 		="postgres";
	$password 	= "postgres";
	$db 			= pg_connect("host=".$host." port=".$port." dbname=".$datenbank." user=".$user." password=".$password);
	
	// alle Hashtags aus der Datenbank entnehmen und im Json-Format speichern
	$sql			= "select * from hashtags;";
	$result 		= pg_query($sql);
	
	//Json-Format
	$hashtags_str	="{ \"hashtags\" :[";
     while($row = pg_fetch_row($result)){
		 $hashtags_str.= "{\"tag\":\"".$row[0]."\"},";
       }
	  
	// Hashtags ueber die decode-Funktion in das json-format umwandeln
 	$hashtags_str	= substr($hashtags_str, 0, -1)."]}";
 	$jsonhash	= json_decode($hashtags_str);
	
	// Hashtags mit den TweetIDs verbinden
	$anzahl 		= pg_query("select tweetid from tweets;");
	$anzahl 		= pg_num_rows ($anzahl);
	$ergebnis 	="{ \"netzwerk\" :[";
	
	for ($i = 0; $i <= $anzahl; $i++){
		    $sql	= "select share.hashtag from tweets,share where tweets.tweetid = share.tweetid and tweets.tweetid = ".$i.";";
		    $r 	= pg_query($sql);
		    $s 	="";
	         while($row = pg_fetch_row($r)){
	    		 $s	.=$row[0];
	           }
			 $ergebnis .= "{\"tags\":\"$s\"},";
		}
	$ergebnis		= substr($ergebnis, 0, -1)."]}";
	$json_ergebnis = json_decode($ergebnis);
	
?><!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.min.js"></script>
	<script src="./sigma/src/sigma.core.js"></script>
	<script src="./sigma/src/conrad.js"></script>
	<script src="./sigma/src/utils/sigma.utils.js"></script>
	<script src="./sigma/src/utils/sigma.polyfills.js"></script>
	<script src="./sigma/src/sigma.settings.js"></script>
	<script src="./sigma/src/classes/sigma.classes.dispatcher.js"></script>
	<script src="./sigma/src/classes/sigma.classes.configurable.js"></script>
	<script src="./sigma/src/classes/sigma.classes.graph.js"></script>
	<script src="./sigma/src/classes/sigma.classes.camera.js"></script>
	<script src="./sigma/src/classes/sigma.classes.quad.js"></script>
	<script src="./sigma/src/classes/sigma.classes.edgequad.js"></script>
	<script src="./sigma/src/captors/sigma.captors.mouse.js"></script>
	<script src="./sigma/src/captors/sigma.captors.touch.js"></script>
	<script src="./sigma/src/renderers/sigma.renderers.canvas.js"></script>
	<script src="./sigma/src/renderers/sigma.renderers.webgl.js"></script>
	<script src="./sigma/src/renderers/sigma.renderers.svg.js"></script>
	<script src="./sigma/src/renderers/sigma.renderers.def.js"></script>
	<script src="./sigma/src/renderers/webgl/sigma.webgl.nodes.def.js"></script>
	<script src="./sigma/src/renderers/webgl/sigma.webgl.nodes.fast.js"></script>
	<script src="./sigma/src/renderers/webgl/sigma.webgl.edges.def.js"></script>
	<script src="./sigma/src/renderers/webgl/sigma.webgl.edges.fast.js"></script>
	<script src="./sigma/src/renderers/webgl/sigma.webgl.edges.arrow.js"></script>
	<script src="./sigma/src/renderers/canvas/sigma.canvas.labels.def.js"></script>
	<script src="./sigma/src/renderers/canvas/sigma.canvas.hovers.def.js"></script>
	<script src="./sigma/src/renderers/canvas/sigma.canvas.nodes.def.js"></script>
	<script src="./sigma/src/renderers/canvas/sigma.canvas.edges.def.js"></script>
	<script src="./sigma/src/renderers/canvas/sigma.canvas.edges.curve.js"></script>
	<script src="./sigma/src/renderers/canvas/sigma.canvas.edges.arrow.js"></script>
	<script src="./sigma/src/renderers/canvas/sigma.canvas.edges.curvedArrow.js"></script>
	<script src="./sigma/src/renderers/canvas/sigma.canvas.edgehovers.def.js"></script>
	<script src="./sigma/src/renderers/canvas/sigma.canvas.edgehovers.curve.js"></script>
	<script src="./sigma/src/renderers/canvas/sigma.canvas.edgehovers.arrow.js"></script>
	<script src="./sigma/src/renderers/canvas/sigma.canvas.edgehovers.curvedArrow.js"></script>
	<script src="./sigma/src/renderers/canvas/sigma.canvas.extremities.def.js"></script>
	<script src="./sigma/src/renderers/svg/sigma.svg.utils.js"></script>
	<script src="./sigma/src/renderers/svg/sigma.svg.nodes.def.js"></script>
	<script src="./sigma/src/renderers/svg/sigma.svg.edges.def.js"></script>
	<script src="./sigma/src/renderers/svg/sigma.svg.edges.curve.js"></script>
	<script src="./sigma/src/renderers/svg/sigma.svg.labels.def.js"></script>
	<script src="./sigma/src/renderers/svg/sigma.svg.hovers.def.js"></script>
	<script src="./sigma/src/middlewares/sigma.middlewares.rescale.js"></script>
	<script src="./sigma/src/middlewares/sigma.middlewares.copy.js"></script>
	<script src="./sigma/src/misc/sigma.misc.animation.js"></script>
	<script src="./sigma/src/misc/sigma.misc.bindEvents.js"></script>
	<script src="./sigma/src/misc/sigma.misc.bindDOMEvents.js"></script>
	<script src="./sigma/src/misc/sigma.misc.drawHovers.js"></script>
	<script src="./sigma/plugins/sigma.plugins.dragNodes/sigma.plugins.dragNodes.js"></script>
	<div id="container">
	  <style>
	    #netzwerk{
	      top: 0;
	      bottom: 0;
	      left: 0;
	      right: 0;
	      position: absolute;
	    }
	  </style>
	  <div id="netzwerk"></div>
	</div>
	<script>
 	    g = {
 	      nodes: [],
 	      edges: []
 	    };
		json_hashtags = '<?php echo json_encode($jsonhash); ?>';
		liste_hashtags = JSON.parse(json_hashtags);
		
		//Liste mit allen Knoten erstellen
		for (i = 0; i < liste_hashtags.hashtags.length; i++)
		  g.nodes.push({
			  id: liste_hashtags.hashtags[i].tag,
		    label: liste_hashtags.hashtags[i].tag,
		    x: Math.random(),
		    y: Math.random(),
		    size: 0.5,
		    color: '#ff001a'
		  });
		  
  		json_ergebnis = '<?php echo json_encode($json_ergebnis); ?>';
  		liste_tweetid_hashtags = JSON.parse(json_ergebnis);
	
	for (i = 0; i < liste_tweetid_hashtags.netzwerk.length; i++){
		array = liste_tweetid_hashtags.netzwerk[i].tags.split("#");
		j = 2
		while( j < array.length){
			if(array[j].length >1){
	   	  	   	// Knoten erstellen
				g.edges.push({
	   	    		 id: "#"+array[1]+"#"+array[j]+i+j,
	   	    		 source: "#"+array[1],
	   	    		 target: "#"+array[j],
	   	    		 size: 0.5,
	   	    		 color: '#ccc'
	   	  	   });
				
		   	}
			j++;
			}
		}
	s = new sigma({
	  graph: g,
	  container: 'netzwerk'
	});

	// Einstellung zum Drag and Drop
	var dragListener = sigma.plugins.dragNodes(s, s.renderers[0]);
	dragListener.bind('startdrag', function(event) {
	  console.log(event);
	});
	dragListener.bind('drag', function(event) {
	  console.log(event);
	});
	dragListener.bind('drop', function(event) {
	  console.log(event);
	});
	dragListener.bind('dragend', function(event) {
	  console.log(event);
	});
	
	
	</script>
	
	  
</head>
<body>
</body>
</html>