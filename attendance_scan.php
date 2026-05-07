<?php
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>AI Attendance Scan</title>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<style>

body{
    margin:0;
    padding:0;
    background:#020617;
    font-family:Arial;
    color:white;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.main{
    width:95%;
    max-width:900px;
}

.card{
    background:#0f172a;
    padding:25px;
    border-radius:20px;
    border:1px solid #1e293b;
}

h1{
    margin-top:0;
    text-align:center;
    margin-bottom:25px;
    font-size:35px;
}

button{
    width:100%;
    padding:15px;
    border:none;
    border-radius:12px;
    background:#38bdf8;
    color:white;
    font-size:16px;
    cursor:pointer;
    margin-bottom:20px;
    font-weight:bold;
}

button:hover{
    background:#0ea5e9;
}

.camera-box{
    position:relative;
    width:100%;
}

video{
    width:100%;
    border-radius:20px;
    background:black;
}

canvas{
    position:absolute;
    top:0;
    left:0;
}

.status{
    margin-top:15px;
    text-align:center;
    color:#38bdf8;
    font-size:18px;
}

#studentCard{
    margin-top:25px;
    background:#1e293b;
    border-radius:20px;
    padding:25px;
    display:none;
    animation:fade .4s ease;
}

#studentImage{
    width:110px;
    height:110px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid #22c55e;
    margin-bottom:15px;
}

#studentName{
    margin:10px 0;
    font-size:28px;
}

#studentEnrollment,
#studentTime{
    color:#cbd5e1;
    font-size:17px;
}

.success{
    margin-top:15px;
    color:#22c55e;
    font-size:22px;
}

@keyframes fade{

    from{
        opacity:0;
        transform:translateY(20px);
    }

    to{
        opacity:1;
        transform:translateY(0px);
    }

}

</style>

</head>

<body>

<div class="main">

<div class="card">

<h1>AI Attendance Scanner</h1>

<button onclick="startCamera()">
Start Camera
</button>

<div class="camera-box">

<video
id="video"
autoplay
muted
playsinline>
</video>

<canvas id="canvas"></canvas>

</div>

<div
class="status"
id="status">

Waiting To Start...

</div>

<div id="studentCard">

<center>

<img
src=""
id="studentImage"
>

<h2 id="studentName"></h2>

<p id="studentEnrollment"></p>

<p id="studentTime"></p>

<h3 class="success">
Attendance Marked Successfully
</h3>

</center>

</div>

</div>

</div>

<script>

const MODEL_URL =
'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';

let faceMatcher;

let stream;

let scanCooldown = false;

/*
LOAD MODELS
*/

async function loadModels(){

    await Promise.all([

        faceapi.nets.tinyFaceDetector.loadFromUri(
            MODEL_URL
        ),

        faceapi.nets.faceLandmark68Net.loadFromUri(
            MODEL_URL
        ),

        faceapi.nets.faceRecognitionNet.loadFromUri(
            MODEL_URL
        )

    ]);

    document.getElementById(
        'status'
    ).innerHTML =
    'AI Models Loaded';

    await loadFaces();

}

/*
LOAD FACES
*/

async function loadFaces(){

    const response =
    await fetch(
        'load_faces.php'
    );

    const data =
    await response.json();

    const grouped = {};

    data.forEach(item=>{

        if(!grouped[item.student_id]){

            grouped[item.student_id] = [];

        }

        grouped[item.student_id].push(

            new Float32Array(

                JSON.parse(
                    item.descriptor
                )

            )

        );

    });

    const labeled = [];

    for(const studentId in grouped){

        labeled.push(

            new faceapi.LabeledFaceDescriptors(

                studentId,

                grouped[studentId]

            )

        );

    }

    faceMatcher =
    new faceapi.FaceMatcher(
        labeled,
        0.5
    );

}

/*
START CAMERA
*/

async function startCamera(){

    stream =
    await navigator.mediaDevices.getUserMedia({

        video:true

    });

    const video =
    document.getElementById(
        'video'
    );

    video.srcObject = stream;

    await video.play();

    document.getElementById(
        'status'
    ).innerHTML =
    'Camera Started';

    detectFaces();

}

/*
FACE DETECTION
*/

async function detectFaces(){

    const video =
    document.getElementById(
        'video'
    );

    const canvas =
    document.getElementById(
        'canvas'
    );

    canvas.width =
    video.videoWidth;

    canvas.height =
    video.videoHeight;

    setInterval(async()=>{

        const detections =

        await faceapi

        .detectAllFaces(

            video,

            new faceapi.TinyFaceDetectorOptions()

        )

        .withFaceLandmarks()

        .withFaceDescriptors();

        const ctx =
        canvas.getContext('2d');

        ctx.clearRect(
            0,
            0,
            canvas.width,
            canvas.height
        );

        detections.forEach(async detection=>{

            const result =
            faceMatcher.findBestMatch(
                detection.descriptor
            );

            const box =
            detection.detection.box;

            /*
            DRAW BOX
            */

            ctx.strokeStyle =
            '#22c55e';

            ctx.lineWidth = 3;

            ctx.strokeRect(

                box.x,
                box.y,
                box.width,
                box.height

            );

            ctx.fillStyle =
            '#22c55e';

            ctx.font =
            '18px Arial';

            ctx.fillText(

                result.label,

                box.x,
                box.y - 10

            );

            /*
            MATCH FOUND
            */

            if(
                result.label != 'unknown'
                &&
                !scanCooldown
            ){

                scanCooldown = true;

                const response =
                await fetch(

                    'mark_attendance.php',

                    {

                        method:'POST',

                        headers:{
                            'Content-Type':'application/json'
                        },

                        body:JSON.stringify({

                            student_id:result.label

                        })

                    }

                );

                const student =
                await response.json();

                showStudent(student);

                setTimeout(()=>{

                    scanCooldown = false;

                },4000);

            }

        });

    },2000);

}

/*
SHOW STUDENT
*/

function showStudent(student){

    document.getElementById(
        'studentCard'
    ).style.display =
    'block';

    /*
    PHOTO
    */

    if(student.photo != ''){

        document.getElementById(
            'studentImage'
        ).src =
        student.photo;

    }

    /*
    NAME
    */

    document.getElementById(
        'studentName'
    ).innerHTML =
    student.name;

    /*
    ENROLLMENT
    */

    document.getElementById(
        'studentEnrollment'
    ).innerHTML =
    'Enrollment : ' +
    student.enrollment_id;

    /*
    DATE + TIME
    */

    document.getElementById(
        'studentTime'
    ).innerHTML =
    'Date : ' +
    student.date +
    '<br><br>Time : ' +
    student.time;

    /*
    STATUS
    */

    if(student.already_marked){

        document.querySelector(
            '.success'
        ).innerHTML =
        'Attendance Already Marked Today';

        document.querySelector(
            '.success'
        ).style.color =
        '#f59e0b';

        document.getElementById(
            'status'
        ).innerHTML =
        student.name +
        ' already marked';

    }else{

        document.querySelector(
            '.success'
        ).innerHTML =
        'Attendance Marked Successfully';

        document.querySelector(
            '.success'
        ).style.color =
        '#22c55e';

        document.getElementById(
            'status'
        ).innerHTML =
        student.name +
        ' attendance marked';

    }

}

/*
LOAD EVERYTHING
*/

window.onload = async()=>{

    await loadModels();

};

</script>

</body>
</html>