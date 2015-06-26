<?php


trait errorHandlerTrait
{


    protected function raiseException( $theException )
    {
    	$cleanMessage = $theException->getMessage();
    	preg_match_all( '/(\/[^ ]*[\.php|\.html|\.xml])/ix' , $cleanMessage , $allPathFound);

    	foreach ($allPathFound[0] as $aPathFound) {
    		$cleanMessage = str_replace($aPathFound, '<strong style="font-weight:bold;">'.$aPathFound.'</strong>', $cleanMessage);
    	}
    	?>
    	<aside style="background:#fff;border:none;float:none;display:table;margin:20px 0;font-size:13px;line-height:18px;">
    		<p>Error on <strong style="font-weight:bold;"><?php echo $theException->getFile(); ?></strong> at line <strong style="font-weight:bold;"><?php echo $theException->getLine(); ?></strong></p><br>
    		<p><?php echo $cleanMessage; ?></p>
			<details style="padding:10px;">
			  <summary>Trace</summary>
			  <p><?php echo nl2br($theException->getTraceAsString()); ?></p>
			</details>
    	</aside>

    	<?php
    }
}