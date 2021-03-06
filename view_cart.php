<?php
session_start();
error_reporting(0);
include ('./config.php');

$total=0;

$albcounter=0;
$trkcounter=0;
$albprice=$downloadCartAlbumPrice;
$trkprice=$downloadCartTrackPrice;
$orderid=microtime(true);
$_SESSION['cart_id']=$orderid;

if (isset($_GET['delete_album'])){
    unset($_SESSION['cart']['album'][$_GET['delete_album']]);
    $_SESSION['cart']['album']=array_values($_SESSION['cart']['album']);
    }

if (isset($_GET['delete_track'])){
    unset($_SESSION['cart']['track'][$_GET['delete_track']]);
    $_SESSION['cart']['track']=array_values($_SESSION['cart']['track']);
    }
?><!DOCTYPE html><html>
<head>
<link rel="shortcut icon" href="<?php echo $favicon;?>" />
<link rel="stylesheet" href="http://<?php echo $server; ?>/style.css" type="text/css" media="screen" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="charset" value="utf-8" />
<title><?php echo strip_tags($title); ?> - Your cart</title>

<?php 
if ($isDownloadCartNameYourPrice){ ?>
<script>
	function compute_total(){
    if (document.getElementById('total')!=null){
		if (!isNaN(parseFloat(document.getElementById('total').value))){
				document.getElementById('amount').value=document.getElementById('total').value;
				if (parseFloat(document.getElementById('total').value)>0){
					document.getElementById('paypal_form').submit();
					
					}
					else {
						document.getElementById('dl').click();
					}
			}
			else{
				alert('please enter a numeric value');
			}
		}
    }
</script>
<?php  } ?>
</head>
<body>
<a href="./">&lt; Go back to the site</a><br/>
<h1>In your cart</h1>
<?php echo count($_SESSION['cart']['album']);?>
 albums and 
<?php echo count($_SESSION['cart']['track']);?>
 individual tracks
 <h2>Albums : </h2>
 <?php
 foreach ($_SESSION['cart']['album'] as $alb){
    echo '<h4 style="display:inline;">'.$alb['title'].'</h4><span style="float:right;">';
    echo count($alb['tracklist']).' tracks';
    $total=$total+$albprice;
	echo ' <a href="?delete_album='.$albcounter.'">X Delete</a></span>';
	echo '<hr/>';
    $albcounter++;
    }
?>
<h2>Tracks : </h2>
 <?php
 foreach ($_SESSION['cart']['track'] as $trk){
    echo '<h4 style="display:inline;">'.$trk['title'].'</h4> by <strong>'.$trk['artist'].'</strong><br/>On <em>'.$trk['album'].'</em><span style="float:right;">';
    $total=$total+$trkprice;
	echo ' <a href="?delete_track='.$trkcounter.'">X Delete</a></span>';
	echo '<hr/>';
    $trkcounter++;
    }



echo '<hr/> '.count($_SESSION['cart']['album']).' times '.htmlspecialchars($downloadCartCurrency.' '.$albprice).' (full albums) plus '.count($_SESSION['cart']['track']).' times '.htmlspecialchars($downloadCartCurrency.' '.$trkprice).' (individual tracks)';
echo '<span style="float:right;"><strong>TOTAL: </strong>';



if ($isDownloadCartNameYourPrice){
		echo htmlspecialchars($downloadCartCurrency). ' ';

		echo ' <input id="total" style="text-align:right;" size="7" type="text" value="';
		echo htmlspecialchars($total);
		echo '"><br/><span style="float:right;font-size:84%;">Name your price, no minimum</span>';
    }
	else{
		echo htmlspecialchars($downloadCartCurrency). ' ';

		echo htmlspecialchars($total);
		
	    }


echo '</span>';
	
 ?>
 <hr/>
<button style="float:right;" onClick="compute_total();" type="button">Process to payment</button>

<form id="paypal_form" name="_xclick" action="https://www.paypal.com/fr/cgi-bin/webscr" method="post" >
<input type="hidden" id="cmd" name="cmd" value="_xclick" />
<input type="hidden" name="custom" value="<?php echo microtime(true);?>"/>
<input type="hidden" name="business" value="<?php echo htmlspecialchars($downloadCartPaypalAddress);?>" />
<input type="hidden" name="item_name" value="<?php echo htmlspecialchars($sitename); ?> - Order <?php echo $orderid ?>" />
<input type="hidden" name="currency_code" value="<?php echo htmlspecialchars($downloadCartCurrency);?>" />
<input type="hidden" id="amount" name="amount" value="<?php echo htmlspecialchars($total);?>" /> 
<input type="hidden" name="return" value="http://<?php echo htmlspecialchars($server);?>/download-page.php"/>
<input type="hidden" name="rm" value="1"/>
<input type="hidden" name="cancel_return" value="http://<?php echo htmlspecialchars($server);?>"/> 
</form>




<?php 
if ($isDownloadCartNameYourPrice){ ?>

<a id="dl" href="download_page.php"></a>

<?php  } ?>


</body>
</html>
