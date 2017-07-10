<?php
	$port = 5432;
	$host = "localhost";
	$datenbank = "election";
	$user ="postgres";
	$password = "postgres";
	$db = pg_connect("host=".$host." port=".$port." dbname=".$datenbank." user=".$user." password=".$password);	
	$sql = "select datum,count(tweets.tweetid) from tweets,share where tweets.tweetid = share.tweetid group by datum order by datum;";
	$result = pg_query($sql);
	$ergebnis ="{ \"anzahldatum\" :[";
     while($row = pg_fetch_row($result)) {
		$datum = $row[0];
		$datum =  str_replace("2016-","",$datum);
		$anzahl = $row[1];
		$ergebnis.= "{\"datum\":\"".$datum."\",\"anzahl\":\"".$anzahl."\"},";
       }
	 $ergebnis= substr($ergebnis, 0, -1)."]}";
	 $json_ergebnis = json_decode($ergebnis);
?><!DOCTYPE html>
<html>
<head>
	<style type="text/css">
	body { font-family: Verdana, Arial, sans-serif; font-size: 12px; }
	#multihashtag{ width: 1250px; height: 450px; }
	</style>
	<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.min.js"></script>
	<script type="text/javascript">
		var erg = '<?php echo json_encode($json_ergebnis); ?>';		
		var obj = JSON.parse(erg);		
		var data = new Array(obj.anzahldatum.length);
		var beschriftung = new Array(obj.anzahldatum.length);
		for (var i = 0; i < obj.anzahldatum.length; i++) {
		  data[i] = new Array(2);
		  beschriftung[i] = new Array(2);
		  data[i][0] = i;
		  beschriftung[i][0] = i;
		  data[i][1] = obj.anzahldatum[i].anzahl;
		  
		  if(i % 5 == 0 )
		  	beschriftung[i][1] = obj.anzahldatum[i].datum;
		  else 
			  beschriftung[i][1] = "";
		}
		
		var dataset = [{ label: "Hashtag-Verlauf", data: data, color: "#FF0000" }];
		var einstellungen = {
		     series: {
		                 bars: {
		                     show: true
		                 }
		             },
		             bars: {
		                 align: "center",
		                 barWidth: 0.5
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
		    $.plot($("#multihashtag"),dataset,einstellungen);
		});
		
		
	</script>
</head>
<body> <div id="multihashtag"></div>
</body>
</html>