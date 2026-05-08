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
    font-family:Arial, sans-serif;
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

/* Top Button */

.top-btn{
    position:fixed;
    top:20px;
    right:20px;
    z-index:1000;

    padding:12px 24px;

    border:none;
    border-radius:50px;

    background:linear-gradient(
        135deg,
        #ff0000,
        #ffcc00
    );

    color:white;
    font-size:16px;
    font-weight:bold;

    cursor:pointer;

    box-shadow:0 4px 15px rgba(0,0,0,0.4);

    transition:0.3s;
}

.top-btn:hover{
    transform:scale(1.05);
}

</style>

</head>

<body>

<!-- Video -->

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

<!-- Top Button -->

<button
class="top-btn"
onclick="goToPage()"
>
Go To Page
</button>

<script>

function goToPage(){

    window.location.href =
    'attendance_scan.php';

}

</script>

</body>
</html>