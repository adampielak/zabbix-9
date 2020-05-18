<?php

require_once '../include/config.inc.php';
require_once('config.php');
require_once 'inc/functions.inc.php';
require_once '../include/hosts.inc.php';
require_once '../include/actions.inc.php';

//Access control
if(!$_COOKIE["zabgraphs_session"]) {
	header("location:index.php");
}

switch (date("m")) {
 case "01": $mes = _('January'); break;
 case "02": $mes = _('February'); break;
 case "03": $mes = _('March'); break;
 case "04": $mes = _('April'); break;
 case "05": $mes = _('May'); break;
 case "06": $mes = _('June'); break;
 case "07": $mes = _('July'); break;
 case "08": $mes = _('August'); break;
 case "09": $mes = _('September'); break;
 case "10": $mes = _('October'); break;
 case "11": $mes = _('November'); break;
 case "12": $mes = _('December'); break;
}

switch (date("w")) {
 case "0": $dia = _('Sunday'); break;    
 case "1": $dia = _('Monday'); break;
 case "2": $dia = _('Tuesday'); break;
 case "3": $dia = _('Wednesday'); break;
 case "4": $dia = _('Thursday'); break;
 case "5": $dia = _('Friday'); break;
 case "6": $dia = _('Saturday'); break;  
}   

//User id 
$userid = get_userid(CWebUser::getSessionCookie());

//zabbis API
require_once 'lib/ZabbixApi.class.php';
use ZabbixApi\ZabbixApi;
$api = new ZabbixApi($zabURL.'api_jsonrpc.php', ''. $zabUser .'', ''. $zabPass .'');

$hostid = array();
$conta = array();

if(isset($_REQUEST['sel']) && $_REQUEST['sel'] != '' && $_REQUEST['sel'] == 1) {
	$group = $_POST['groupid'];
}	

//check version
if(ZABBIX_EXPORT_VERSION >= '4.0'){
	$grps = 'hstgrp';
}
else {
	$grps = 'groups';
}

//treeview groups	
$dbGroups = DBselect( 'SELECT * FROM '.$grps.' WHERE name NOT LIKE "%Templa%" ORDER BY name ASC');
$groupID = array();

if(isset($_REQUEST['search']) AND $_REQUEST['search'] != '') {
	$search = $_REQUEST['search'];
	$dbHostsname = DBselect ('SELECT DISTINCT g.groupid AS grid FROM hosts h, hosts_groups hg, '.$grps.' g WHERE h.status = 0 AND h.hostid = hg.hostid AND hg.groupid = g.groupid AND h.name LIKE "%'.$search.'%" ORDER BY grid ASC');
	$hostsnames = array();
	
	while($row = DBFetch($dbHostsname)){
		$groupID[] = $row['grid'];
	}
}
	else {
	while ($groups = DBFetch($dbGroups)) {		
		$groupID[] = $groups['groupid'];										
	}	
}

//get selected hosts
if(isset($_REQUEST['hostid'])) {	
	$hostid = explode(",",$_REQUEST['hostid']);
	$sel_hosts = implode(",",$hostid); 	  
}
// case no hosts, use zabbix server by default
else {
	//$dbZabServer = DBselect( 'SELECT hostid FROM hosts WHERE name LIKE "Zabbix server"');
	$dbZabServer = DBselect( 'SELECT hostid FROM hosts WHERE name NOT LIKE "%Templa%" ORDER by hostid ASC LIMIT 1');
	$hostid_res = DBFetch($dbZabServer);
	$hostid[] = $hostid_res['hostid'];
	$sel_hosts = implode(",",$hostid); 	  
}

//time period
if(isset($_REQUEST['from'])) {	
	$from = $_REQUEST['from'];
	$to = $_REQUEST['to'];
	$from_val = $from;
	$to_val = $to;
}
else {
	$from = date('Y-m-d H:i:s', strtotime('-1 hour'));	
	$to = date('Y-m-d H:i:s');	
	$from_val = $from;
	$to_val = $to;
}

if(isset($_REQUEST['period'])) {	
	$period = $_REQUEST['period'];
	$from = 'now-'.$period;
	$to = 'now';
	$from_val = date('Y-m-d H:i:s', strtotime('-1 hour'));	
	$to_val = date('Y-m-d H:i:s');	
}
else {
	$period = 60;
	if(isset($_REQUEST['from'])) {	
		$from = $_REQUEST['from'];
		$to = $_REQUEST['to'];
		$from_val = $from;
		$to_val = $to;
	}	
}	

if(isset($_REQUEST['item'])) {
	$item = $_REQUEST['item'];
}
else {
	$item = '';
}	  
?>

<!DOCTYPE html>
<html>
<head>
    <title>ZabGraphs: Home</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	 <meta http-equiv="Pragma" content="public">
    <meta http-equiv="refresh" content= "600"/>
    
    <link rel="icon" href="img/favicon.ico" type="image/x-icon" />
	 <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />    
    <link href="css/bootstrap.css" rel="stylesheet">     
    <link href="css/bootstrap.css.map" rel="stylesheet">    
    <script src="js/jquery.min.js"></script> 

    <!-- Styles -->   
    <!-- Color theme -->       		   
    <link rel="stylesheet" type="text/css" href="css/layout.css">
    
     <!-- this page specific styles -->
<!--    <link rel="stylesheet" href="css/index.css" type="text/css" media="screen" />    -->

    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
<!--    <link href="css/style-dash.css" rel="stylesheet" type="text/css" />    -->
    
    <!-- odometer -->
<!--	<link href="css/odometer.css" rel="stylesheet">
	<script src="js/odometer.js"></script>-->
    
   <!-- <link href="less/style.less" rel="stylesheet"  title="lessCss" id="lessCss"> -->

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
     <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
     <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
     <![endif]-->         
     <!-- <link href="fonts/fonts.css" rel="stylesheet" type="text/css" /> -->
      
   <link rel="stylesheet" type="text/css" href="./css/skin-material.css">
 	<link rel="stylesheet" type="text/css" href="./css/style-material.css">
 	<link href="css/font-awesome.css" rel="stylesheet">  	
 	
<!-- 	<script src="js/jquery-ui-1.10.2.custom.min.js"></script>-->
   
	<script src="js/bootstrap.min.js"></script> 
	<script src="js/bootstrap-switch.js"></script> 
	<script src="js/jquery.accordion.js"></script>            
	<script src="js/bootstrap-dropdown.js"></script>
	<script src="js/jquery.address-1.6.min.js"></script>
	<script src="js/jquery.easy-pie-chart.js"></script> 
	
	<script src="js/theme.js"></script>         
	<script src="js/jquery.jclock.js"></script>
 	
<!-- 	<script src="js/jquery-3.3.1.slim.min.js"></script>	-->
	<link href="css/bootree.css" rel="stylesheet">
	<script src="js/bootree.js"></script>	
	
	<link href="css/bootstrap-datetimepicker.css" rel="stylesheet" media="screen">
	<script type="text/javascript" src="js/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>	
	
	<script type="text/javascript" src="js/gallery.js"></script>
	<link rel="stylesheet" href="inc/datatables/css/dataTables.bootstrap.min.css"> 
	<script src="inc/datatables/js/jquery.dataTables.min.js"></script>
	<script src="inc/datatables/js/dataTables.bootstrap.min.js"></script>
 	 
 	 <script type="text/javascript">
		function scrollWin()
		{
			$('html, body').animate({ scrollTop: 0 }, 'slow');
		}
 	 </script>

	<style type="text/css">
		.loader { height: 110% !important;}
	</style>
	
	<link href="css/loader.css" type="text/css" rel="stylesheet" />
	
	<script type="text/javascript">
		jQuery(window).load(function () {
			$(".loader").fadeOut("slow"); 
			$("#container-fluid").toggle("fast");    
		});
	</script>
 	  	 
</head>

<body style="width: 100%;">
<div id="loader" class="loader"></div>
<!--   <div class='container-fluidx col-md-3'>-->
   <div class="container col-md-12 col-sm-12">
   <div class="row">
       <!-- .navbar -->
       <nav class="navbar navbar-default nav-delighted navbar-fixed-top shad2" role="navigation" >
        <a href="#" class="toggle-left-sidebar">
            <i class="fa fa-th-list"></i>
        </a>

        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header" style="color:#fff;" >
            <a class="navbar-brand" href="../index.php" target="_blank">
            <span><img src="img/zabbix.png" alt="Zabbix" style="height:24px !important; "></img></span></a>
        </div>
		<!-- NAVBAR LEFT  -->					
		<ul id="navbar-left" class="nav navbar-nav pull-left hidden-xs">
		    <li class="logo">
		        <a href="./zabgraphs.php" style="margin-top:6px;">           
		            <span class="name" style="color: #FFF; font-size:14pt;">
		                ZabGraphs  
		            </span>            
		        </a>
		    </li>
		</ul>
       								
		<!-- /NAVBAR LEFT -->					
		<ul class="nav navbar-nav pull-right hidden-xs">
			<li id="header-user" class="user" style="color:#FFF; margin-top: 8px; margin-right:8px;">
				<span><?php //echo $newversion; ?></span>						
				<span class="username">				
				</span>
				<div id="clockx" style="text-align:right;"></div>
				<div id="logout" style="text-align:right; padding-top:10px;">
					<i class='fa fa-info-circle' title='Info' style='color:#fff; font-size:16px; cursor:pointer; padding-right: 30px;' onclick="alert('Version: <?php echo $version; ?> \nhttps://github.com/stdonato/zabgraphs');"></i>
					<i class='fa fa-power-off' title='Exit' style='color:#fff; font-size:18px; cursor:pointer; padding-right: 15px;' onclick="window.open('logout.php','_self');"></i>
				</div>
			</li>
		</ul>  																
   <!-- /.navbar-collapse -->																																	
	               
      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav navbar-right">
              <li>                                    
              </li>
              <li>                                   
              </li>
          </ul>
      </div>
      <!-- /.navbar-collapse -->                         
     </nav>
					
			 <div class="tree_menu col-md-2 col-sm-2" style="margin-top: 60px; margin-left: -5px; width: 290px;">
				<div id="host_search" class="row" style="width: 250px !important;">
					<div class="input-group" style="padding-left: 10px;" >
						<input id="search_hosts" type="text" name="search_host" style="width: 230px;" placeholder="<?php echo $labels['Host search']; ?>" class="form-control" value="<?php echo $search; ?>">
						<span class="input-group-btn" style="float: left;" >						
							<button class="btn btn-primary btn-sm" type="submit" name="btnSearch" id="btnSearch" >
								<i class="fa fa-search"></i>
							</button>
						</span>
					</div>	
				</div>
				<div class="row">
					<div id="tree" style="text-align: left; margin-bottom: 25px;"></div>
				</div>
			 </div>
				
			<script type="text/javascript" >
			
			$(document).ready(function () {
				var tree = $('#tree').tree({
			     primaryKey: 'id',
			     uiLibrary: 'bootstrap',
			     dataSource: [
				
				<?php
				
				foreach( $groupID as $g ) {
					
					$dbGroups = DBselect( 'SELECT groupid, name FROM '.$grps.' WHERE groupid ='.$g );
					$dbHostsCount = DBselect( 'SELECT COUNT(h.hostid) AS conta FROM hosts h, hosts_groups hg, '.$grps.' g WHERE h.status = 0 AND h.hostid = hg.hostid AND hg.groupid = g.groupid AND g.groupid ='. $g.' ORDER BY h.name ASC' );
					//$dbHosts = DBselect( 'SELECT DISTINCT h.hostid AS hostid, h.name AS hostname, g.name AS grname, g.groupid AS grid FROM hosts h, hosts_groups hg, hstgrp g WHERE h.status = 0 AND h.hostid = hg.hostid AND hg.groupid = g.groupid AND g.groupid ='. $g.' ORDER BY h.name ASC' );
					
					if($search == '') {			
						$dbHosts = DBselect( 'SELECT DISTINCT h.hostid AS hostid, h.name AS hostname, g.name AS grname, g.groupid AS grid FROM hosts h, hosts_groups hg, '.$grps.' g WHERE h.status = 0 AND h.hostid = hg.hostid AND hg.groupid = g.groupid AND g.groupid ='. $g.' ORDER BY h.name ASC' );
						$dbHostsCount = DBselect( 'SELECT COUNT(h.hostid) AS conta FROM hosts h, hosts_groups hg, '.$grps.' g WHERE h.status = 0 AND h.hostid = hg.hostid AND hg.groupid = g.groupid AND g.groupid ='. $g.'');
					}
					else {		
						$dbHosts = DBselect( 'SELECT DISTINCT h.hostid AS hostid, h.name AS hostname, g.name AS grname, g.groupid AS grid FROM hosts h, hosts_groups hg, '.$grps.' g WHERE h.status = 0 AND h.hostid = hg.hostid AND hg.groupid = g.groupid AND g.groupid ='. $g.' AND h.name LIKE "%'.$search.'%" ORDER BY h.name ASC' );
						$dbHostsCount = DBselect( 'SELECT COUNT(h.hostid) AS conta FROM hosts h, hosts_groups hg, '.$grps.' g WHERE h.status = 0 AND h.hostid = hg.hostid AND hg.groupid = g.groupid AND g.groupid ='. $g.' AND h.name LIKE "%'.$search.'%"' );
					}
				
					$groups = DBFetch($dbGroups);
					$hostscount = DBFetch($dbHostsCount);
								
					echo "{'id':".$groups['groupid'].",";
					echo "'text':'".$groups['name']." (".$hostscount['conta'].")',\n";	
					//echo "'text':'".$groups['name']."',";	
					echo "children:[";
					
				
						while ($hosts = DBFetch($dbHosts)) {
						   echo "{'id':".$hosts['hostid'].",";
							echo "'text':'".$hosts['hostname']."'},";	
					
						}	
					echo "],";
					echo "},\n";
					
				}
				
				?>		
				],
			       checkboxes: true
			       
			       });
			       $('#btnSave').on('click', function () {
			           var checkedIds = tree.getCheckedNodes();
			           var search_item = document.getElementById("search_item").value;
			           var from = document.getElementById("from").value;
			           var to = document.getElementById("to").value;
			           var hosts = document.getElementById("hostsid").value;
			           var search = document.getElementById("search_hosts").value;
			           //alert(checkedIds);
			           //alert('hosts: '+hosts);
			           if (checkedIds == '') {			           		
			               window.open('zabgraphs.php?hostid='+hosts+'&from='+decodeURI(from)+'&to='+decodeURI(to)+'&item='+search_item+'&search='+search ,'_self');			           	
			           }
			           else {
			               window.open('zabgraphs.php?hostid='+checkedIds+'&from='+decodeURI(from)+'&to='+decodeURI(to)+'&item='+search_item+'&search='+search ,'_self');			           	
			           }
			       });

					$('#btnSearch').on('click', function () {					        
					        var search = document.getElementById("search_hosts").value;
					        //alert(search);					        
					        window.open('zabgraphs.php?search='+search ,'_self');
					 }); 		       			       
			       
				 });				
				</script>

		<div id="graficos" class="container-fluid col-lg-10 col-md-8 col-sm-8 col-xs-6" style="margin-top:70px;">
		
		<div id="time" class="row col-md-12 col-sm-12" style="float: left;" >
			<div class="dates col-lg-6 col-md-10 col-sm-12 pull-left">
				<span style="margin-top: 10px;"> 				
				  <input id="from" type="text" name="from" class="form-control input-sm" class="field" style="width: 140px; display: inline-block; " placeholder="from" value="<?php echo $from_val; ?>" />
				  <input id="to" type="text" name="to" class="form-control input-sm" class="field" style="width: 140px; display: inline-block; " placeholder="to" value="<?php echo $to_val; ?>" />
				  Item: <input id="search_item" type="text" name="item" class="form-control input-sm" class="field" style="width: 130px; display: inline-block; " placeholder="ex: cpu" value="<?php echo $item; ?>"/>
				  <input id="hostsid" type="hidden" value="<?php echo $sel_hosts; ?>" />
				</span>  
				<span class="btn-group">
				  <button id="btnSave" type="button" class="btn btn-primary" style="margin-left: 20px;"><i class="fa fa-ok"></i><?php echo $labels['Send']; ?></button>
				</span>
			</div>
			<div class="row col-lg-6 col-md-6  col-sm-6" id="buttons" style="margin-bottom:-60px;">
				<span class="btn-group pull-right">
					  <button type="button" class="btn btn-primary" onclick='location.href="zabgraphs.php?period=5m&hostid=<?php echo $sel_hosts;?>&item=<?php echo $item;?>&search=<?php echo $search; ?>";'>5m</button>		
					  <button type="button" class="btn btn-primary" onclick='location.href="zabgraphs.php?period=30m&hostid=<?php echo $sel_hosts;?>&item=<?php echo $item;?>&search=<?php echo $search; ?>";'>30m</button>
					  <button type="button" class="btn btn-primary" onclick='location.href="zabgraphs.php?period=60m&hostid=<?php echo $sel_hosts;?>&item=<?php echo $item;?>&search=<?php echo $search; ?>";'>1h</button>		
					  <button type="button" class="btn btn-primary" onclick='location.href="zabgraphs.php?period=6h&hostid=<?php echo $sel_hosts;?>&item=<?php echo $item;?>&search=<?php echo $search; ?>";'>6h</button>
					  <button type="button" class="btn btn-primary" onclick='location.href="zabgraphs.php?period=12h&hostid=<?php echo $sel_hosts;?>&item=<?php echo $item;?>&search=<?php echo $search; ?>";'>12h</button>
					  <button type="button" class="btn btn-primary" onclick='location.href="zabgraphs.php?period=1d&hostid=<?php echo $sel_hosts;?>&item=<?php echo $item;?>&search=<?php echo $search; ?>";'>1d</button>		
					  <button type="button" class="btn btn-primary" onclick='location.href="zabgraphs.php?period=7d&hostid=<?php echo $sel_hosts;?>&item=<?php echo $item;?>&search=<?php echo $search; ?>";'>7d</button>
					  <button type="button" class="btn btn-primary" onclick='location.href="zabgraphs.php?period=30d&hostid=<?php echo $sel_hosts;?>&item=<?php echo $item;?>&search=<?php echo $search; ?>";'>1m</button>		
				</span>
			</div>
		</div>
		
		<div id="graphs" class="" style="margin-top:10px; margin-bottom: 50px; float:none; margin-right:auto; margin-left:auto; text-align:center; padding-left: 15px;">	
			
			<div class='row'>
				<?php		
				foreach( $hostid as $h ) {
				
					// get all graphs
					 $graphs = $api->graphGet(array(
					     'output' => 'extend',
					     'hostids' => $h,       
					     'search' => array('name' => $item),  
					     'sortfield' => 'name',
						  'sortorder' => 'ASC'
					));
					
					if(count($graphs) > 0) {
					
					//divide array em celulas
					if(count($graphs) > 1) {
						$rows = array_chunk($graphs,2);
						$conta = [1,2];			
					}
					else {
						$rows = array_chunk($graphs,1);
						$conta = [1];
					}
				
					echo "<h5 style='display:block; color:#000 !important; float:none; margin-right:auto; margin-left:auto; margin-bottom: 6px; margin-top:35px;' class='well well-sm'> ".get_hostname($h)."</h5>\n";
					
					?>
					<table id="" class="display table table-condensed table-hover" >
					<thead style="display: none;">
						<tr>
						<?php
							foreach($conta as $c) {
								echo "<th></th>\n"; 				
							}
						?>
						</tr>
					</thead>
					<tbody>
					<?php
					
					if(count($rows) > 1) {
						
						foreach ($rows as $row) {
							//insert rows to be a even number			
							if(count($row) == 1) {
								array_push($row,'cp');
							}

							echo "<tr>\n";
							
							foreach($row as $g) {	
						
								echo "<td>\n";
								echo "<div class='thumb' style='padding: 5px !important; margin-bottom:0px;'>";
										if($g != 'cp') {								
											echo '<a class="thumbnail" href="#" data-image-id="" data-toggle="modal" data-title="" data-caption="" data-image="../chart2.php?graphid='.$g->graphid.'&from='.$from.'&to='.$to.'&profileIdx=web.graphs.filter&profileIdx2='.$g->graphid.'&width=1200&height=320" alt="" data-target="#image-gallery">';
											echo '<img class="img-responsive ximg-thumbnail ximg-rounded" src="'.$zabURL.'chart2.php?graphid='.$g->graphid.'&from='.$from.'&to='.$to.'&profileIdx=web.graphs.filter&profileIdx2='.$g->graphid.'&height=250" />';
										} else {
											echo '<a class="thumbnailx" href="#" data-image-id="" data-toggle="modal" data-title="" data-caption="" data-image="img/blank.png" alt="" data-target="#image-galleryx">';
											echo '<img class="img-responsive ximg-thumbnail ximg-rounded" src="img/blank.png" />';									
										}
									echo '</a>';
								echo "</div>\n";
								echo "</td>\n";		
							}	
							echo "</tr>\n";		
						}	
					}
					else {
						
						foreach ($rows as $row) {													
						echo "<tr>\n";
						
						foreach($row as $g) {	
					
							echo "<td>\n";
							echo "<div class='thumb' style='padding: 5px !important; margin-bottom:0px;'>";
								echo '<a class="thumbnail" href="#" data-image-id="" data-toggle="modal" data-title="" data-caption="" data-image="../chart2.php?graphid='.$g->graphid.'&from=now'.$from.'&to='.$to.'&profileIdx=web.graphs.filter&profileIdx2='.$g->graphid.'&width=1200&height=320" alt="" data-target="#image-gallery">';
									echo '<img class="img-responsive ximg-thumbnail ximg-rounded" src="'.$zabURL.'chart2.php?graphid='.$g->graphid.'&from='.$from.'&to='.$to.'&profileIdx=web.graphs.filter&profileIdx2='.$g->graphid.'&height=250" />';
								echo '</a>';
							echo "</div>\n";
							echo "</td>\n";		
						}	
						echo "</tr>\n";		
						}
					}
						echo "</tbody>\n";
						echo "</table>\n";
						//echo "</div>\n";
					} //end if graph > 0
				} //end foreach hostid
				?>
		
			</div>	
		</div>
		
		<!--modal-->
		<div class="modal fade" id="image-gallery" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		    <div class="modal-dialog" style="width:1200px;">
		        <div class="modal-content" style="width:1200px;">
		            <div class="modal-header">
		                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
		                <h5 class="modal-title" id="image-gallery-title"></h5>
		            </div>
		            <div class="modal-body">
		                <img id="image-gallery-image" class="img-responsive" src="">
		            </div>
		            <div class="modal-footer">
		
		                <div class="col-md-2">
		                    <button type="button" class="btn btn-primary" id="show-previous-image"><?php echo $labels['Previous']; ?></button>
		                </div>
		
		                <div class="col-md-8 text-justify" id="image-gallery-caption">                    
		                </div>
		
		                <div class="col-md-2">
		                    <button type="button" id="show-next-image" class="btn btn-default"><?php echo $labels['Next']; ?></button>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		</div> <!-- graficos -->
	</div>
</div>

<script>

$(document).ready(function() {
    $('table.display').DataTable( {
        "paging":   true,
        "ordering": false,
        "info":     true,
        "searching":   false,
        //"scrollY":     '65vh',
        //"scrollCollapse": true,
        "lengthMenu": [[2, 5, 10, 25, 50, -1], [2, 5, 10, 25, 50, "All"]],
        "dom": '<"top"<"clear">>rt<"bottom"iflp<"clear">>'
    } ); 
} );

//datetime
$("#from").datetimepicker({
	format: 'yyyy-mm-dd hh:ii:ss',
	autoclose: true,
   todayBtn: true,
   startDate: "2018-02-14 10:00:00",
   minuteStep: 5
	});

$("#to").datetimepicker({
	format: 'yyyy-mm-dd hh:ii:ss',
	autoclose: true,
   todayBtn: true,
   startDate: "2019-02-14 10:00:00",
   minuteStep: 5
	});


</script>

<!-- /.site-holder -->
 <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
 <!-- Include all compiled plugins (below), or include individual files as needed -->


 <!-- Remove below two lines in production -->  
 <script src="js/theme-options.js"></script>       
 <script src="js/core.js"></script>
 
</body>
</html>
