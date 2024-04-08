<?php
$timestamp = time();
echo date("m/d H:i");

$h = date("G");
$m = date("i");
$s = date("s");
?>

<HTML>
<TITLE>
    Time
</TITLE>

<HEAD>
    <SCRIPT Language="JavaScript">
        timer = 1;

        function Init() {
            h = <?= $h ?>;
            m = <?= $m ?>;
            s = <?= $s ?>;
            PrintTime();
        }

        function PrintTime() {
            if (s == 60) {
                s = 0;
                m++;
            }
            if (m == 60) {
                m = 0;
                h++;
            }
            if (h == 24) h = 0;
            status = h + ":" + m + ":" + s;
            s = s + 1;
            clearTimeout(timer);
            timer = setTimeout("PrintTime()", 1000);
        }
    </SCRIPT>
</HEAD>

<BODY bgcolor="#FFFFFF" onLoad="Init()">
</BODY>

</HTML>