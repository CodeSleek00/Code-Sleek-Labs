<?php
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Attendance Scan</title>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,100..900;1,100..900&display=swap');

:root {
    /* 70% White Base */
    --white-50: #ffffff;
    --white-100: #f8fafc;
    --white-200: #f1f5f9;
    --white-300: #e2e8f0;
    --white-400: #cbd5e1;
    
    /* 20% Blue Primary */
    --blue-50: #eff6ff;
    --blue-100: #dbeafe;
    --blue-200: #bfdbfe;
    --blue-300: #93c5fd;
    --blue-400: #60a5fa;
    --blue-500: #3b82f6;
    --blue-600: #2563eb;
    --blue-700: #1d4ed8;
    --blue-800: #1e40af;
    --blue-900: #1e3a8a;
    
    /* 10% Accent - Purple/Gold */
    --accent-500: #8b5cf6;
    --accent-600: #7c3aed;
    --accent-700: #6d28d9;
    --accent-gold: #f59e0b;
    
    --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    
    --radius-sm: 0.5rem;
    --radius-md: 0.75rem;
    --radius-lg: 1rem;
    --radius-xl: 1.5rem;
    --radius-full: 9999px;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: var(--white-100);
    color: var(--blue-900);
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 1.5rem;
}

.main {
    width: 100%;
    max-width: 560px;
    margin: 0 auto;
}

/* Card - 70% White */
.card {
    background: var(--white-50);
    border-radius: var(--radius-xl);
    padding: 1.75rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--white-300);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: var(--shadow-xl);
}

/* Header - Blue Gradient */
h1 {
    font-size: 1.75rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 1.75rem;
    background: linear-gradient(135deg, var(--blue-700), var(--blue-600));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    letter-spacing: -0.025em;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

h1::before {
    content: '🎓';
    font-size: 1.75rem;
    background: none;
    -webkit-background-clip: unset;
    background-clip: unset;
    color: var(--blue-500);
}

/* Button - 20% Blue with Accent hover */
button {
    width: 100%;
    padding: 0.875rem 1.5rem;
    border: none;
    border-radius: var(--radius-md);
    background: var(--blue-600);
    color: white;
    font-family: 'Inter', sans-serif;
    font-size: 0.9375rem;
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    box-shadow: var(--shadow-sm);
}

button::before {
    content: '📷';
    font-size: 1.125rem;
}

button:hover {
    background: var(--accent-600);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

button:active {
    transform: translateY(0);
}

/* Circular Camera Container - White with Blue Border */
.camera-box {
    position: relative;
    width: 100%;
    aspect-ratio: 1 / 1;
    border-radius: 50%;
    overflow: hidden;
    background: var(--white-200);
    box-shadow: var(--shadow-lg);
    border: 3px solid var(--blue-500);
    transition: all 0.3s ease;
}

.camera-box:hover {
    border-color: var(--accent-500);
    transform: scale(1.01);
    box-shadow: var(--shadow-xl);
}

video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    background: var(--white-300);
}

canvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    pointer-events: none;
}

/* Status - Blue Background */
.status {
    text-align: center;
    margin-top: 1rem;
    padding: 0.625rem 1rem;
    background: var(--blue-50);
    border-radius: var(--radius-full);
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--blue-700);
    display: inline-block;
    width: auto;
    margin-left: auto;
    margin-right: auto;
    border: 1px solid var(--blue-100);
}

/* Student Result Card - White Card */
#studentCard {
    margin-top: 1.5rem;
    background: var(--white-50);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    display: none;
    animation: slideUp 0.4s ease;
    border: 1px solid var(--white-300);
    box-shadow: var(--shadow-md);
    position: relative;
    overflow: hidden;
}

#studentCard::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--blue-500), var(--accent-500), var(--accent-gold));
}

#studentCard img {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--blue-500);
    box-shadow: var(--shadow-md);
    margin-bottom: 0.75rem;
}

#studentCard h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--blue-800);
    margin-bottom: 0.25rem;
}

#studentCard p {
    font-size: 0.8125rem;
    color: var(--blue-600);
    margin: 0.25rem 0;
}

/* Success Text - Blue */
.success {
    color: var(--blue-600);
    font-weight: 600;
}

/* Warning Text - Accent */
.warning {
    color: var(--accent-gold);
    font-weight: 600;
}

/* Animations */
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Loading State */
.loading {
    position: relative;
    pointer-events: none;
    opacity: 0.7;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid var(--blue-200);
    border-top-color: var(--blue-600);
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

/* Responsive Design */
@media (max-width: 640px) {
    body {
        padding: 1rem;
    }
    
    .card {
        padding: 1.25rem;
    }
    
    h1 {
        font-size: 1.5rem;
        margin-bottom: 1.25rem;
    }
    
    h1::before {
        font-size: 1.5rem;
    }
    
    button {
        padding: 0.75rem 1.25rem;
        font-size: 0.875rem;
    }
    
    #studentCard {
        padding: 1.25rem;
    }
    
    #studentCard img {
        width: 72px;
        height: 72px;
    }
    
    #studentCard h2 {
        font-size: 1.125rem;
    }
}

@media (max-width: 480px) {
    .card {
        padding: 1rem;
    }
    
    h1 {
        font-size: 1.25rem;
    }
    
    .status {
        font-size: 0.75rem;
        padding: 0.5rem 0.875rem;
    }
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: var(--white-200);
    border-radius: var(--radius-full);
}

::-webkit-scrollbar-thumb {
    background: var(--blue-400);
    border-radius: var(--radius-full);
}

::-webkit-scrollbar-thumb:hover {
    background: var(--blue-500);
}

/* Focus States */
button:focus-visible {
    outline: 2px solid var(--blue-500);
    outline-offset: 2px;
}

/* Camera Active Animation */
.camera-box.active {
    animation: cameraPulse 2s infinite;
}

@keyframes cameraPulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
        border-color: var(--blue-500);
    }
    50% {
        box-shadow: 0 0 0 12px rgba(59, 130, 246, 0);
        border-color: var(--accent-500);
    }
}

/* Student Details Icons */
#studentEnrollment::before {
    content: '📋';
    margin-right: 0.25rem;
}

#studentTime::before {
    content: '⏰';
    margin-right: 0.25rem;
}

center {
    display: flex;
    flex-direction: column;
    align-items: center;
}
</style>

</head>

<body>

<div class="main">

<div class="card">

<h1>AI Attendance Scan</h1>

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

<div class="status" id="status">
Waiting To Start...
</div>

<div id="studentCard">

<center>

<img
src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
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

let modelsLoaded = false;

let faceMatcher;

let stream;

let scannedStudents = {};

async function loadModels(){

    await Promise.all([

        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),

        faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),

        faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)

    ]);

    modelsLoaded = true;

    document.getElementById(
        'status'
    ).innerHTML =
    'AI Models Loaded';

    await loadFaces();

}

window.onload = async()=>{

    await loadModels();

};

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

            if(result.label != 'unknown'){

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

            }

        });

    },2500);

}

function showStudent(student){

    document.getElementById(
        'studentCard'
    ).style.display =
    'block';

    document.getElementById(
        'studentName'
    ).innerHTML =
    student.name;

    document.getElementById(
        'studentEnrollment'
    ).innerHTML =
    'Enrollment : ' +
    student.enrollment_id;

    document.getElementById(
        'studentTime'
    ).innerHTML =
    'Time : ' +
    student.time;

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

</script>

</body>
</html>