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
    --primary-50: #eef2ff;
    --primary-100: #e0e7ff;
    --primary-200: #c7d2fe;
    --primary-300: #a5b4fc;
    --primary-400: #818cf8;
    --primary-500: #6366f1;
    --primary-600: #4f46e5;
    --primary-700: #4338ca;
    --primary-800: #3730a3;
    --primary-900: #312e81;
    
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    
    --success-50: #ecfdf5;
    --success-500: #10b981;
    --success-600: #059669;
    
    --warning-50: #fffbeb;
    --warning-500: #f59e0b;
    --warning-600: #d97706;
    
    --error-50: #fef2f2;
    --error-500: #ef4444;
    --error-600: #dc2626;
    
    --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.05);
    --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    
    --radius-sm: 0.375rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
    --radius-2xl: 1.5rem;
    --radius-full: 9999px;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
    color: var(--gray-100);
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 1.5rem;
    position: relative;
    overflow-x: hidden;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 20% 50%, rgba(99, 102, 241, 0.1) 0%, transparent 50%);
    pointer-events: none;
}

.main {
    width: 100%;
    max-width: 560px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

/* Modern Card */
.card {
    background: rgba(30, 41, 59, 0.95);
    backdrop-filter: blur(10px);
    border-radius: var(--radius-2xl);
    padding: 1.75rem;
    border: 1px solid rgba(99, 102, 241, 0.2);
    box-shadow: var(--shadow-2xl);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Header */
h1 {
    font-size: 1.75rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 1.75rem;
    background: linear-gradient(135deg, #ffffff 0%, #a5b4fc 100%);
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
    color: var(--primary-400);
}

/* Modern Button */
button {
    width: 100%;
    padding: 0.875rem 1.5rem;
    border: none;
    border-radius: var(--radius-lg);
    background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
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
    position: relative;
    overflow: hidden;
}

button::before {
    content: '📷';
    font-size: 1.125rem;
}

button:hover {
    background: linear-gradient(135deg, var(--primary-700), var(--primary-800));
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

button:active {
    transform: translateY(0);
}

/* Circular Camera Container */
.camera-box {
    position: relative;
    width: 100%;
    aspect-ratio: 1 / 1;
    border-radius: 50%;
    overflow: hidden;
    background: linear-gradient(135deg, var(--gray-800), var(--gray-900));
    box-shadow: var(--shadow-2xl);
    border: 3px solid rgba(99, 102, 241, 0.3);
    transition: all 0.3s ease;
}

.camera-box:hover {
    border-color: var(--primary-500);
    transform: scale(1.01);
}

video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    background: var(--gray-900);
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

/* Status Badge */
.status {
    text-align: center;
    margin-top: 1rem;
    padding: 0.625rem 1rem;
    background: rgba(99, 102, 241, 0.1);
    border-radius: var(--radius-full);
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--primary-300);
    display: inline-block;
    width: auto;
    margin-left: auto;
    margin-right: auto;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(99, 102, 241, 0.2);
}

/* Student Result Card */
#studentCard {
    margin-top: 1.5rem;
    background: linear-gradient(135deg, var(--gray-800), var(--gray-900));
    padding: 1.5rem;
    border-radius: var(--radius-xl);
    display: none;
    animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(99, 102, 241, 0.2);
    position: relative;
    overflow: hidden;
}

#studentCard::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-500), var(--success-500), var(--warning-500));
}

#studentCard img {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--primary-500);
    box-shadow: var(--shadow-lg);
    margin-bottom: 0.75rem;
}

#studentCard h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.25rem;
}

#studentCard p {
    font-size: 0.8125rem;
    color: var(--gray-400);
    margin: 0.25rem 0;
}

#studentCard .success,
#studentCard .warning {
    font-size: 0.875rem;
    font-weight: 600;
    margin-top: 0.75rem;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-full);
    display: inline-block;
}

.success {
    color: var(--success-500);
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.warning {
    color: var(--warning-500);
    background: rgba(245, 158, 11, 0.1);
    border: 1px solid rgba(245, 158, 11, 0.2);
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

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
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
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
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
    background: var(--gray-800);
    border-radius: var(--radius-full);
}

::-webkit-scrollbar-thumb {
    background: var(--primary-600);
    border-radius: var(--radius-full);
}

::-webkit-scrollbar-thumb:hover {
    background: var(--primary-500);
}

/* Focus States */
button:focus-visible {
    outline: 2px solid var(--primary-400);
    outline-offset: 2px;
}

/* Camera Box Active State */
.camera-box.active {
    animation: cameraPulse 2s infinite;
}

@keyframes cameraPulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4);
        border-color: rgba(99, 102, 241, 0.3);
    }
    50% {
        box-shadow: 0 0 0 12px rgba(99, 102, 241, 0);
        border-color: var(--primary-500);
    }
}

/* Student Info Grid */
.student-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.student-details {
    margin-top: 0.5rem;
}

.student-details p {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.student-details p::before {
    font-size: 0.875rem;
}

#studentEnrollment::before {
    content: '📋';
    margin-right: 0.25rem;
}

#studentTime::before {
    content: '⏰';
    margin-right: 0.25rem;
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