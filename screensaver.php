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

    background:
    linear-gradient(
        135deg,
        #0f172a,
        #1e3a8a,
        #7c3aed
    );

    color:white;

    position:relative;
}

.container{

    text-align:center;
    animation:fadeIn 1s ease;
}

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

h1{

    font-size:3rem;
    font-weight:800;

    letter-spacing:2px;

    margin-bottom:10px;
}

p{

    font-size:1.1rem;

    opacity:0.8;
}

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

.glow{

    position:absolute;

    width:400px;
    height:400px;

    background:#3b82f6;

    filter:blur(140px);

    opacity:0.25;

    border-radius:50%;

    z-index:-1;
}

.glow.one{
    top:-100px;
    left:-100px;
}

.glow.two{
    bottom:-100px;
    right:-100px;
    background:#7c3aed;
}

</style>

</head>

<body>

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
    'attendance.php';

}

['mousemove','click','keypress','touchstart']
.forEach(event=>{

    document.addEventListener(
        event,
        returnToAttendance
    );

});

</script>

</body>
</html>