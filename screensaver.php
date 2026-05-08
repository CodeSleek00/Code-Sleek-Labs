<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>FAIZ COMPUTER INSTITUTE</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    width:100%;
    height:100vh;

    overflow:hidden;

    background:black;
}

/* Fullscreen Video */

.video-bg{

    position:fixed;

    top:0;
    left:0;

    width:100%;
    height:100%;

    object-fit:cover;
}

</style>

</head>

<body>

<video
class="video-bg"
autoplay
muted
loop
playsinline
>

<source
src="FAIZ.mp4"
type="video/mp4"
>

</video>
<!--
<script>

function returnToAttendance(){

    window.location.href =
    'attendance_scan.php';

}

[
'mousemove',
'click',
'keypress',
'touchstart'
]
.forEach(event=>{

    document.addEventListener(
        event,
        returnToAttendance
    );

});

</script>-->
</body>
</html>