<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>FAIZ COMPUTER INSTITUTE</title>

<style>

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    font-family:'Inter',sans-serif;

    width:100%;
    height:100vh;

    overflow:hidden;

    display:flex;
    justify-content:center;
    align-items:center;

    color:white;

    position:relative;

    background:black;
}

/* Background Video */

.video-bg{

    position:absolute;

    top:0;
    left:0;

    width:100%;
    height:100%;

    object-fit:cover;

    z-index:-3;
}

/* Dark Overlay */

.overlay{

    position:absolute;

    inset:0;

    background:
    linear-gradient(
        rgba(0,0,0,0.55),
        rgba(0,0,0,0.75)
    );

    z-index:-2;
}

/* Glow Effects */

.glow{

    position:absolute;

    width:400px;
    height:400px;

    filter:blur(140px);

    opacity:0.25;

    border-radius:50%;

    z-index:-1;
}

.glow.one{

    top:-100px;
    left:-100px;

    background:#3b82f6;
}

.glow.two{

    bottom:-100px;
    right:-100px;

    background:#7c3aed;
}

/* Main Container */

.container{

    text-align:center;

    animation:fadeIn 1s ease;

    padding:20px;

    backdrop-filter:blur(6px);

    background:rgba(255,255,255,0.05);

    border:1px solid rgba(255,255,255,0.08);

    border-radius:25px;

    box-shadow:
    0 0 40px rgba(0,0,0,0.35);
}

/* Logo */

.logo{

    width:140px;
    height:140px;

    border-radius:50%;

    object-fit:cover;

    border:4px solid rgba(255,255,255,0.2);

    box-shadow:
    0 0 50px rgba(255,255,255,0.15);

    margin-bottom:25px;

    animation:float 3s ease-in-out infinite;
}

/* Text */

h1{

    font-size:3rem;
    font-weight:800;

    letter-spacing:2px;

    margin-bottom:10px;

    text-shadow:
    0 0 20px rgba(255,255,255,0.3);
}

p{

    font-size:1.1rem;

    opacity:0.85;
}

/* Scan Line */

.scan-line{

    position:absolute;

    width:100%;
    height:4px;

    background:
    linear-gradient(
        90deg,
        transparent,
        #22c55e,
        transparent
    );

    animation:scan 3s linear infinite;
}

/* Animations */

@keyframes scan{

    0%{
        top:-10%;
    }

    100%{
        top:110%;
    }

}

@keyframes float{

    0%,100%{
        transform:translateY(0);
    }

    50%{
        transform:translateY(-15px);
    }

}

@keyframes fadeIn{

    from{
        opacity:0;
        transform:scale(0.95);
    }

    to{
        opacity:1;
        transform:scale(1);
    }

}

/* Responsive */

@media(max-width:768px){

    h1{
        font-size:2rem;
    }

    p{
        font-size:0.95rem;
    }

    .logo{
        width:110px;
        height:110px;
    }

}

</style>

</head>

<body>

<!-- Background Video -->

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

<div class="overlay"></div>

<div class="glow one"></div>
<div class="glow two"></div>

<div class="scan-line"></div>

<div class="container">

<img
src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
class="logo"
>

<h1>
FAIZ COMPUTER INSTITUTE
</h1>

<p>
Move mouse or press any key to continue
</p>

</div>

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

</script>

</body>
</html>