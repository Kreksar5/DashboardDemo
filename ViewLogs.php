<!DOCTYPE html>
<html>
<body>
<button type="button" onclick="window.close()">Close Log</button>
<pre style="word-wrap: break-word; white-space: pre-wrap; font-size: 20px; margin:0px;"><?php echo file_get_contents("BillboardLog.txt");?></pre>
</body>
</html>