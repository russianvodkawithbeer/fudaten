<?php
	$hashtag = $_POST["hashtag"];
	if(substr($hashtag,0,1) != "#")
		$hashtag = "#".$hashtag;
	
	$port = 5432;
	$host = "localhost";
	$datenbank = "election";
	$user ="postgres";
	$password = "postgres";
	$db = pg_connect("host=".$host." port=".$port." dbname=".$datenbank." user=".$user." password=".$password);
	
	$sql = "select datum,count(tweets.tweetid) from tweets,share where tweets.tweetid = share.tweetid and share.hashtag = '".$hashtag."' group by datum  order by datum;";
	$result = pg_query($sql);
	$ergebnis ="{ \"anzahldatum\" :[";
     while($row = pg_fetch_row($result)) {
		$datum = $row[0];
		$anzahl = $row[1];
		$ergebnis.= "{\"datum\":\"".$datum."\",\"anzahl\":\"".$anzahl."\"},";
       }
	 $ergebnis= substr($ergebnis, 0, -1)."]}";
	 $json_hashtag = json_decode("{\"hashtag\":[{\"tag\":\".$hashtag.\"}]}");
	 $json_ergebnis = json_decode($ergebnis);
	
?><!DOCTYPE html>
<html>
<head>
	<style type="text/css">
	body { font-family: Verdana, Arial, sans-serif; font-size: 12px; }
	#singlehashtag{ width: 900px; height: 450px; }
	</style>
	<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.min.js"></script>
	<script type="text/javascript">
		var erg = '<?php echo json_encode($json_ergebnis); ?>';
		var hashtag = '<?php echo json_encode($json_hashtag);?>';
		var o = JSON.parse(hashtag);
		var hashtag = (o.hashtag[0].tag);
		
		
		var obj = JSON.parse(erg);		
		var data = new Array(obj.anzahldatum.length);
		var beschriftung = new Array(obj.anzahldatum.length);
		for (var i = 0; i < obj.anzahldatum.length; i++) {
		  data[i] = new Array(2);
		  beschriftung[i] = new Array(2);
		  data[i][0] = i;
		  beschriftung[i][0] = i;
		  data[i][1] = obj.anzahldatum[i].anzahl;
		 
		  
		  if(i % 2 == 0 )
		  	beschriftung[i][1] = obj.anzahldatum[i].datum;
		  else 
			  beschriftung[i][1] = "";
		}
		
		var dataset = [{ label: "Häufigkeit des Hashtags " +hashtag, data: data, color: "#FF0000" }];
		var einstellungen = {
		     series: {
		                 bars: {
		                     show: true
		                 }
		             },
		             bars: {
		                 align: "center",
		                 barWidth: 0.3
		             },
				   
			        legend: {
			                   noColumns: 0,
			                   labelBoxBorderColor: "#000000",
			                   position: "nw",
					    axisLabelFontSizePixels: 20,
			               },
						
					     xaxis: {
					                  axisLabelFontSizePixels: 12,
					                  axisLabelFontFamily: 'Verdana, Arial',
					                  axisLabelPadding: 10,
							ticks: beschriftung
					              },
							    
 
			  };
		$(document).ready(function () {
		    $.plot($("#singlehashtag"),dataset,einstellungen);
		});
		
		
	</script>
</head>
<body> <div id="singlehashtag"></div>
</body>
</html>