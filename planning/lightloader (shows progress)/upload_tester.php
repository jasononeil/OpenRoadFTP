<?php 
$session = md5(uniqid(rand(), true));
$upload_fields = 3;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
<title>SeeMySites.net - AJAX Upload Demo</title>
<link href="upload.css" type="text/css" rel="stylesheet"/>
<script type="text/javascript" src="json_c.js"></script>
<script type="text/javascript" src="upload_form.js"></script>
<script type="text/javascript" src="sr_c.js"></script>

</head>
<body>

Go to <a href="http://www.seemysites.net/projFolder/uploader">SeeMySites.net</a> for the latest updates.
<div style="width: 800px; margin: 2em auto;">
 <h1 style="font-size: 150%;">Upload Multiple Files at Once:</h1>
  <div style="width: 40em; border: 3px ridge #ff0000; padding: .5em">
    <?php for ($i=0; $i < $upload_fields; $i++) { ?>
      <form METHOD="POST" enctype="multipart/form-data" name="form<?php echo $i; ?>" id="form<?php echo $i; ?>" action="upload.cgi?sID=<?php echo $session; ?>form<?php echo $i; ?>" target="form<?php echo $i; ?>_iframe">
    	  <div class="progressBox"><div class="progressBar" id="<?php echo $session . 'form' .$i.'_progress'; ?>">&nbsp;</div></div>
        <div class="fileName" id="<?php echo $session . 'form' .$i.'_fileName'; ?>"></div>
        <input style="" type="file" name="file<?php echo $i; ?>" onchange="uploadForm('<?php echo 'form'.$i; ?>', '<?php echo $session . 'form' .$i; ?>');" />
    	</form>
    	<iframe name="form<?php echo $i; ?>_iframe" id="form<?php echo $i; ?>_iframe" src="blank.html" class="loader"></iframe> 
  	<?php } ?>
    
  </div>

  <div class="mainDiv" style="width: 40em; border: 0;">
    <p style="text-align: center;">Use this HTML file to test out your server to make sure that everything is working.</p>
  </div>
</div>



</body>
</html>
