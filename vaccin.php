<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="beau.css" rel="stylesheet">    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
	<title>TDC</title>

</head>
<body>




	<?php
		try {
			$db=new PDO('mysql:host=localhost;dbname=db-tellieno','usr-tellieno','2AhjAnwOSE');
		}

		catch (Exception $e)
		{

			die('Erreur : ' . $e->getMessage());
		}		


//on traite les données entrées dans le formulaire de date   
if(!empty($_GET['date'])){
  $date = $_GET['date'];
  $ddate = new DateTime($date);
  $week = $ddate->format("Y-W");
  $dateprecise = $ddate->format('l d F Y');

}

else{
  $date = '2022-11-06';
  $ddate = new DateTime($date);
  $week = $ddate->format("Y-W");
  $dateprecise = $ddate->format('l d F Y');
}




//on traite les données entrées dans le formulaire de vaccin 
if(!empty($_POST['vaccin'])){
  $vacc = $_POST['vaccin'];
}

else{
  $vacc = 'Tout vaccin';
}

        
        
  // vaccins en fonction de l'âge
 

	$datas=$db -> prepare("SELECT `effectif_cumu_termine`, `libelle_classe_age` FROM `donnees_vaccinations` WHERE `type_vaccin`='$vacc' AND `classe_age`!='TOUT_AGE'AND `semaine_injection`='$week'");

	$datas -> execute();

	$vaccinations = $datas -> fetchAll();

	foreach($vaccinations as $vaccination){
        $classe_age[] = $vaccination['libelle_classe_age'];
        $tab_donnees[] = $vaccination['effectif_cumu_termine'];
    }
  
  if(empty($tab_donnees)){
    $tabvide = 1;

  }

  else{
    $tabvide = 0;
  }

  //evolution morts et hospitalisés

  $dchosp=$db -> prepare("SELECT `incid_dchosp`, `date`,`pos`, `incid_hosp` FROM `donnees_malades` WHERE `date` <= '$date'");

	$dchosp -> execute();

	$décés = $dchosp -> fetchAll();

	foreach($décés as $décé){
        $datedc[] = $décé['date'];
        $dchosps[] = $décé['incid_dchosp'];
        $hospi[] = $décé['incid_hosp'];
        $pos[] = $décé['pos'];
    }


//hospitalisations 
$hosp=$db -> prepare("SELECT `incid_hosp` FROM `donnees_malades` WHERE `date` = '$date'");

$hosp -> execute();

$hospitalisations = $hosp -> fetchAll();

foreach($hospitalisations as $hospitalisation){
      $newhosps = $hospitalisation['incid_hosp'];
  }

$tothosp = $db -> prepare("SELECT SUM(incid_hosp) FROM `donnees_malades` WHERE `date` <= '$date'");
$tothosp -> execute();

$total = $tothosp -> fetchAll();

foreach($total as $tot){
  $totalhosp = $tot['SUM(incid_hosp)'];
}
   
   
   
//décés
$décéstout=$db -> prepare("SELECT `incid_dchosp` FROM `donnees_malades` WHERE `date` = '$date'");

$décéstout -> execute();

$décétout = $décéstout -> fetchAll();

foreach($décétout as $décétt){
      $newdécé = $décétt['incid_dchosp'];

  }

$totdécé = $db -> prepare("SELECT SUM(incid_dchosp) FROM `donnees_malades` WHERE `date` <= '$date'");
$totdécé-> execute();

$totdécés = $totdécé-> fetchAll();

foreach($totdécés as $ttdécé){
  $totaldc = $ttdécé['SUM(incid_dchosp)'];
}

//nombre de cas positifs 
$postout=$db -> prepare("SELECT `pos` FROM `donnees_malades` WHERE `date` = '$date'");

$postout -> execute();

$postout = $postout -> fetchAll();

foreach($postout as $postt){
      $newpos = $postt['pos'];
      if($newpos==" "){
        $newpos = 0;
      }

  }

$totpos = $db -> prepare("SELECT SUM(pos) FROM `donnees_malades` WHERE `date` <= '$date'");
$totpos-> execute();

$totposs = $totpos-> fetchAll();

foreach($totposs as $ttpos){
  $totalpos = $ttpos['SUM(pos)'];
}

//reas

$reastout=$db -> prepare("SELECT `incid_rea` FROM `donnees_malades` WHERE `date` = '$date'");

$reastout -> execute();

$reatout = $reastout -> fetchAll();

foreach($reatout as $reatt){
      $newrea = $reatt['incid_rea'];

  }

$totrea = $db -> prepare("SELECT SUM(incid_rea) FROM `donnees_malades` WHERE `date` <= '$date'");
$totrea-> execute();

$totreas = $totrea-> fetchAll();

foreach($totreas as $ttrea){
  $totalrea = $ttrea['SUM(incid_rea)'];
}

//NOMBRE DE VACCINES PAR VACCINS

//Pfizer
$nbvacpfizer = $db -> prepare("SELECT SUM(effectif_termine) FROM `donnees_vaccinations` WHERE `type_vaccin` LIKE '%Pfizer%'");
$nbvacpfizer-> execute();
$totvacpfizer = $nbvacpfizer->fetchAll();

foreach($totvacpfizer as $ttvac){
  $totalvaccpfizer= $ttvac['SUM(effectif_termine)'];
}

//astrazeneca
$nbvacastra = $db -> prepare("SELECT SUM(effectif_termine) FROM `donnees_vaccinations` WHERE `type_vaccin`='VAXZEVRIA AstraZeneca'");
$nbvacastra-> execute();
$totvacastra = $nbvacastra->fetchAll();

foreach($totvacastra as $ttastra){
  $totalvaccastra= $ttastra['SUM(effectif_termine)'];
}

//moderna
$nbvacmoderna = $db -> prepare("SELECT SUM(effectif_termine) FROM `donnees_vaccinations` WHERE `type_vaccin`='SPIKEVAX Moderna'");
$nbvacmoderna-> execute();
$totvacmoderna = $nbvacmoderna->fetchAll();

foreach($totvacmoderna as $ttmoderna){
  $totalvaccmoderna= $ttmoderna['SUM(effectif_termine)'];
}


//janssen 

$nbvacjanssen = $db -> prepare("SELECT SUM(effectif_termine) FROM `donnees_vaccinations` WHERE `type_vaccin`='JCOVDEN Janssen'");
$nbvacjanssen-> execute();
$totvacjanssen = $nbvacjanssen->fetchAll();

foreach($totvacjanssen as $ttjanssen){
  $totalvaccjanssen= $ttjanssen['SUM(effectif_termine)'];
}

//novavax
$nbvacnova = $db -> prepare("SELECT SUM(effectif_termine) FROM `donnees_vaccinations` WHERE `type_vaccin`='NUVAXOVID Novavax'");
$nbvacnova-> execute();
$totvacnova = $nbvacnova->fetchAll();

foreach($totvacnova as $ttnova){
  $totalvaccnova= $ttnova['SUM(effectif_termine)'];
}



// NOMBRE DE RAD PAR RAPPORT AUX HOSPITALISATIONS
//Hosp 2020
$tothosp2020 = $db -> prepare("SELECT SUM(incid_hosp) FROM `donnees_malades` WHERE `date` <= '2020-12-31'");
$tothosp2020 -> execute();

$total2020 = $tothosp2020 -> fetchAll();

foreach($total2020 as $tot2020){
  $totalhosp2020 = $tot2020['SUM(incid_hosp)'];
}

//Hosp 2021
$tothosp2021 = $db -> prepare("SELECT SUM(incid_hosp) FROM `donnees_malades` WHERE `date` BETWEEN '2021-01-01' AND '2021-12-31'");
$tothosp2021 -> execute();

$total2021 = $tothosp2021 -> fetchAll();

foreach($total2021 as $tot2021){
  $totalhosp2021 = $tot2021['SUM(incid_hosp)'];
}

//Hosp 2022

$tothosp2022 = $db -> prepare("SELECT SUM(incid_hosp) FROM `donnees_malades` WHERE `date` BETWEEN '2022-01-01' AND '2022-11-10'");
$tothosp2022 -> execute();

$total2022 = $tothosp2022 -> fetchAll();

foreach($total2022 as $tot2022){
  $totalhosp2022 = $tot2022['SUM(incid_hosp)'];
}

//Rad 2020
$totrad2020 = $db -> prepare("SELECT SUM(incid_rad) FROM `donnees_malades` WHERE `date` <= '2020-12-31'");
$totrad2020 -> execute();

$totalrad2020 = $totrad2020 -> fetchAll();

foreach($totalrad2020 as $totrad2020){
  $totalrads2020 = $totrad2020['SUM(incid_rad)'];
}

//Rad 2021
$totrad2021 = $db -> prepare("SELECT SUM(incid_rad) FROM `donnees_malades` WHERE `date` BETWEEN '2021-01-01' AND '2021-12-31'");
$totrad2021 -> execute();

$totalrad2021 = $totrad2021 -> fetchAll();

foreach($totalrad2021 as $totrad2021){
  $totalrads2021 = $totrad2021['SUM(incid_rad)'];
}

//Rad 2022
$totrad2022 = $db -> prepare("SELECT SUM(incid_rad) FROM `donnees_malades` WHERE `date` BETWEEN '2022-01-01' AND '2022-11-10'");
$totrad2022 -> execute();

$totalrad2022 = $totrad2022 -> fetchAll();

foreach($totalrad2022 as $totrad2022){
  $totalrads2022 = $totrad2022['SUM(incid_rad)'];
}

//Décés 2020
$totdc2020 = $db -> prepare("SELECT SUM(incid_dchosp) FROM `donnees_malades` WHERE `date` <= '2020-12-31'");
$totdc2020 -> execute();

$totaldc2020 = $totdc2020 -> fetchAll();

foreach($totaldc2020 as $totdc2020){
  $totaldcs2020 = $totdc2020['SUM(incid_dchosp)'];
}

//Rad 2021
$totdc2021 = $db -> prepare("SELECT SUM(incid_dchosp) FROM `donnees_malades` WHERE `date` BETWEEN '2021-01-01' AND '2021-12-31'");
$totdc2021 -> execute();

$totaldc2021 = $totdc2021 -> fetchAll();

foreach($totaldc2021 as $totdc2021){
  $totaldcs2021 = $totdc2021['SUM(incid_dchosp)'];
}

//Rad 2022
$totdc2022 = $db -> prepare("SELECT SUM(incid_dchosp) FROM `donnees_malades` WHERE `date` BETWEEN '2022-01-01' AND '2022-11-10'");
$totdc2022 -> execute();

$totaldc2022 = $totdc2022 -> fetchAll();

foreach($totaldc2022 as $totdc2022){
  $totaldcs2022 = $totdc2022['SUM(incid_dchosp)'];
}


?>


<header class="site-header">
  <img src="images/covid19.png" style="width: 40px; height: 40px; margin-left: 1vw">
  <div class="site-header">
    <h2 class="intro" style="margin-left:10px; font-size: 1.5vw;">Suivi du COVID-19 dans le Nord : <?php echo $dateprecise?></h2>
    <div class="form-date"><form method="get"> <label for="date">Sélectionner une date</label>

<input type="date" id="date" name="date"
       value="2022-11-06"
       min="2020-03-18" max="2022-11-06">
<input type="hidden" value = "form-date">
<input type ="submit" value="Valider"></form></div>
  </div></form>
</header>

<!-- News à gauche -->
<div id="contain">
<div id="info"><div id="contain-info" style="margin-top: 0px";><h4>Cas confirmés</h4><p><b><?php echo $totalpos?></b></p>(valeur cumulée)<p> + <?php echo $newpos?> cas</p></div>
<div id="contain-info"><h4>Hospitalisations</h4><p><B><?php echo $totalhosp?></b></p>(valeur cumulée)<p> + <?php echo $newhosps?> cas</p></div>
<div id="contain-info"><h4>Réanimations</h4><p><b><?php echo $totalrea?></b></p>(valeur cumulée)<p> + <?php echo $newrea?> cas</P></div>
<div id="contain-info"><h4>Décés</h4><p><b><?php echo $totaldc?></b></p>(valeur cumulée)<p> + <?php echo $newdécé?> décés</P></div>
</div>

<!-- Graphiques à gauche -->

<div id="left">

<!-- Premier graphique ( doughnut ) -->

<div class="form-vaccin"><form method="post" id="formu-vaccin"> <label for="date">Sélectionner un type de vaccin</label>

<select name="vaccin" id="vaccin">
    <option value="">--Choisissez un type de vaccin--</option>
    <option value="COMIRNATY Pfizer-BioNTech">Pfizer</option>
    <option value="VAXZEVRIA AstraZeneca">Astrazeneca</option>
    <option value="SPIKEVAX Moderna">Moderna</option>
    <option value="JCOVDEN Janssen">Janssen</option>
    <option value="NUVAXOVID Novavax">Novavax</option>
    <option value="COMIRNATY Pfizer-BioNTech pédiatrique">Pfizer pédiatrique</option>
    <option value="Tout vaccin">Tout type de vaccin</option>
</select>

<input type ="submit" value="Valider"></form></div>


<?php if($tabvide==0){
?>
<div id="doughnut"> 
    <canvas id="doughnut-chart">Désolé, votre navigateur ne prend pas en charge &lt;canvas&gt;.</canvas>
</div>
<?php 
}


else{
  
?> <div id="tabvide">Pas de données à la date sélectionnée (pas de vaccination en cours)</div>
  <?php
}?>




<!-- Deuxième graphique ( barre ) -->
<canvas id="bar-chart"></canvas>
</div>


<!-- Graphiques à droite -->
<!-- Troisième graphique (lignes positifs / décés ) -->
<div id="right">
<div id="line"> 
    <canvas id="line_chart">Désolé, votre navigateur ne prend pas en charge &lt;canvas&gt;.</canvas>
</div>
<div id="right-bottom">
<div id="bar-chart-mixed">
<canvas id="bar-chart-grouped" width="800" height="450"></canvas></div>

<div id="mixed-chartt">
<canvas id="mixed-chart" width="800" height="450"></canvas></div>
</div></div>
</div id="contain">

<script>

//graph vaccinés/âges doughnut




new Chart(document.getElementById("doughnut-chart"), {
    type: 'doughnut',
    data: {
      labels: <?php if ($tabvide==0) {echo json_encode($classe_age);} else {echo "[0,0,0,0,0,0,0,0]";}?>,
      datasets: [
        {
          backgroundColor: ["#3e95cd", "#7bb4ff", "	#8e5ea2","		#1da2d8","		#363b74","	#493267", "	#064273", "#76b6c4"],
          data: <?php if ($tabvide==0) {echo json_encode($tab_donnees);} else {echo "[0,0,0,0,0,0,0,0]";}?>,
        }
      ]
    },

    options: {
        plugins: { 
            title: {
            display: true,
            text: "Vaccinations par classe d'âge pour <?php echo ($vacc)?>" ,
            color: 'rgb(255,255,255)'
      }

    }
     
    }
});


//graph indicateurs

console.log(date)
data = <?php echo json_encode($pos)?>;
data2 = <?php echo json_encode($dchosps)?>;
data3 = <?php echo json_encode($hospi)?>;


new Chart(document.getElementById("line_chart"), {
                type: 'line',
                data: {
                  labels: <?php echo json_encode($datedc); ?>,
                  datasets: [{ 
                      data: data2,
                      label: "Nombre de personnes décédées",
                      backgroundColor : "#b3cde0",
                      borderColor: "#b3cde0",
                      fill: false,
                      borderWidth:1,
                      pointRadius: 0,
                    },
                    { 
                      type: 'line',
                      data: data,
                      label: "Nombre de personnes testées positives",
                      backgroundColor : "#8e5ea2",
                      borderColor: "#8e5ea2",
                      fill: false,
                      borderWidth:1,
                      pointRadius: 0,
                  
                      
                    },
                    { 
                      type: 'line',
                      data: data3,
                      label: "Nombre de personnes hospitalisées",
                      backgroundColor : "#3cba9f",
                      borderColor: "#3cba9f",
                      fill: false,
                      borderWidth:1,
                      pointRadius: 0,
                  
                     
                    },
                    ]
                },
                options:{
                    interaction: {
                        intersect: false
                        },
                    plugins: {
                        legend: true,
                        title: {
                              display: true,
                              text: 'Evolution des indicateurs reliés au COVID-19',
                              color: 'rgb(255,255,255)'
                        }
                    },
               
                },
              
              });

// Bar chart

datapfizer = <?php echo json_encode($totalvaccpfizer)?>;
datamoderna = <?php echo json_encode($totalvaccmoderna)?>;
dataastra = <?php echo json_encode($totalvaccastra)?>;
datanovavax = <?php echo json_encode($totalvaccnova)?>;
datajanssen = <?php echo json_encode($totalvaccjanssen)?>;
new Chart(document.getElementById("bar-chart"), {
    type: 'bar',
    data: {
      labels: ["Pfizer", "Moderna", "Janssen", "Novavax", "Astrazeneca"],
      datasets: [
        {
          label: "Population",
          backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#363b74"],
          data: [datapfizer,datamoderna,datajanssen,datanovavax,dataastra]
        }
      ]
    },
    options: {
    plugins: {
      title: {
        display: true,
        text: "Nombre d'injections par type de vaccin",
        color: 'rgb(255,255,255)',
        align: 'center'
      }
    }
  }
});

datarad2020 = <?php echo json_encode($totalrads2020)?>;
datarad2021 = <?php echo json_encode($totalrads2021)?>;
datarad2022 = <?php echo json_encode($totalrads2022)?>;

datahosp2020 = <?php echo json_encode($totalhosp2020)?>;
datahosp2021 = <?php echo json_encode($totalhosp2021)?>;
datahosp2022 = <?php echo json_encode($totalhosp2022)?>;

datadcs2020 = <?php echo json_encode($totaldcs2020)?>;
datadcs2021 = <?php echo json_encode($totaldcs2021)?>;
datadcs2022 = <?php echo json_encode($totaldcs2022)?>;
new Chart(document.getElementById("bar-chart-grouped"), {
    type: 'bar',
    data: {
      labels: ["2020", "2021", "2022"],
      datasets: [
        {
          label: "Hospitalisations",
          backgroundColor: "#3e95cd",
          data: [datahosp2020,datahosp2021,datahosp2022]
        }, {
          label: "Retours à domiciles",
          backgroundColor: "#8e5ea2",
          data: [datarad2020, datarad2021, datarad2022]
        }
      ]
    },
    options: {
      title: {
        display: true,
        text: 'Personne hospitalisées vs personnes de retour à domicile par an'
      }
    }
});

new Chart(document.getElementById("mixed-chart"), {
    type: 'bar',
    data: {
      labels: ["2020", "2021", "2022"],
      datasets: [{
          label: "Hospitalisations",
          type: "line",
          borderColor: "#e8c3b9",
          data: [datahosp2020,datahosp2021,datahosp2022],
          fill: false
        }, {
          label: "Décés hospitalisations",
          type: "line",
          borderColor: "#76b6c4",
          data: [datadcs2020,datadcs2021,datadcs2022],
          fill: false
        }, {
          label: " ",
          type: "bar",
          backgroundColor: "#3e95cd",
          data: [datahosp2020,datahosp2021,datahosp2022],
        }, {
          label: "Décés hospitalisations",
          type: "bar",
          backgroundColor: "#3cba9f",
          backgroundColorHover: "#3cba9f",
          data: [datadcs2020,datadcs2021,datadcs2022],
        }
      ]
    },
    options: {
      title: {
        display: false,
        text: 'Personne hospitalisées et personnes de retour décédées par an'
      },
      legend:{
        display: false
      }
    }
});
</script>

</body>
</html>