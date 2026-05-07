<?php
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>AI Face Attendance System</title>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,100..900;1,100..900&display=swap');

:root {
    /* Primary Colors - Educational Trust Blue */
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
    
    /* Neutral Colors - Clean Minimal */
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
    
    /* Success Colors */
    --success-50: #ecfdf5;
    --success-500: #10b981;
    --success-600: #059669;
    
    /* Warning/Accent Colors */
    --accent-50: #fff7ed;
    --accent-500: #f59e0b;
    --accent-600: #d97706;
    
    /* Shadows */
    --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.05);
    --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    
    /* Border Radius */
    --radius-sm: 0.375rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
    --radius-2xl: 1.5rem;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
    color: var(--gray-800);
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 2rem;
}

/* Modern Header */
h1 {
    background: linear-gradient(135deg, var(--gray-900) 0%, var(--primary-700) 100%);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: -0.025em;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    position: relative;
    padding-bottom: 1rem;
}

h1::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-500), var(--primary-300));
    border-radius: var(--radius-full);
}

h1::before {
    content: '🎓';
    font-size: 2rem;
    background: none;
    -webkit-background-clip: unset;
    background-clip: unset;
    color: var(--primary-600);
}

h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    letter-spacing: -0.025em;
}

h2::before {
    content: '';
    width: 4px;
    height: 20px;
    background: var(--primary-500);
    border-radius: var(--radius-full);
}

/* Modern Cards - Glassmorphism */
.card {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(10px);
    border-radius: var(--radius-2xl);
    padding: 1.75rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: var(--shadow-lg);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-200);
}

/* Modern Buttons */
button {
    font-family: 'Inter', sans-serif;
    font-weight: 500;
    font-size: 0.875rem;
    padding: 0.625rem 1.25rem;
    border-radius: var(--radius-lg);
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    letter-spacing: -0.01em;
}

button:active {
    transform: scale(0.98);
}

/* Primary Button */
button:first-of-type {
    background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
    color: white;
    box-shadow: var(--shadow-sm);
}

button:first-of-type:hover {
    background: linear-gradient(135deg, var(--primary-700), var(--primary-800));
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Secondary Button (Accent) */
button:nth-child(2) {
    background: white;
    color: var(--primary-700);
    border: 1.5px solid var(--primary-200);
}

button:nth-child(2):hover {
    background: var(--primary-50);
    border-color: var(--primary-400);
    transform: translateY(-2px);
}

/* Tertiary Button */
button:nth-child(3) {
    background: var(--gray-100);
    color: var(--gray-700);
    border: 1px solid var(--gray-200);
}

button:nth-child(3):hover {
    background: var(--gray-200);
    color: var(--gray-900);
    transform: translateY(-2px);
}

/* Select Dropdown - Modern */
select {
    width: 100%;
    padding: 0.75rem 1rem;
    border-radius: var(--radius-lg);
    border: 1.5px solid var(--gray-200);
    background: white;
    font-family: 'Inter', sans-serif;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--gray-800);
    transition: all 0.2s ease;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1rem;
}

select:hover {
    border-color: var(--primary-300);
}

select:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px var(--primary-100);
}

/* Circular Camera Container - Professional */
.camera-box {
    position: relative;
    width: 100%;
    max-width: 560px;
    margin: 1.5rem auto;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: var(--shadow-2xl);
    background: linear-gradient(135deg, var(--gray-800), var(--gray-900));
    aspect-ratio: 1 / 1;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 3px solid white;
}

.camera-box::before {
    content: '📷';
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 10;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    padding: 0.5rem;
    border-radius: 50%;
    font-size: 1rem;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.camera-box:hover::before {
    opacity: 1;
}

.camera-box:hover {
    transform: scale(1.02);
    box-shadow: var(--shadow-2xl);
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

/* Photo Container - Grid Layout */
#photoContainer {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 80px));
    gap: 1rem;
    margin-top: 1.5rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: var(--radius-xl);
    justify-content: center;
}

.photoBox {
    position: relative;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid white;
    box-shadow: var(--shadow-md);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

.photoBox::after {
    content: '✓';
    position: absolute;
    bottom: 0;
    right: 0;
    width: 24px;
    height: 24px;
    background: var(--success-500);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    transform: scale(0);
    transition: transform 0.2s ease;
}

.photoBox:hover::after {
    transform: scale(1);
}

.photoBox:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-500);
}

.photoBox img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Status Messages - Toast-like */
.status {
    padding: 0.75rem 1rem;
    border-radius: var(--radius-lg);
    font-size: 0.875rem;
    font-weight: 500;
    margin-top: 1rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--gray-100);
    color: var(--gray-700);
    border-left: 3px solid var(--primary-500);
}

#registerStatus::before {
    content: 'ℹ️';
    font-size: 1rem;
}

#attendanceStatus::before {
    content: '👤';
    font-size: 1rem;
}

/* Student List - Modern Cards */
.studentBox {
    background: white;
    padding: 1rem 1.25rem;
    border-radius: var(--radius-lg);
    margin-bottom: 0.75rem;
    border: 1px solid var(--gray-200);
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}

.studentBox:hover {
    transform: translateX(4px);
    border-color: var(--primary-200);
    box-shadow: var(--shadow-md);
}

.studentBox h3 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.studentBox p {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin: 0;
}

.present {
    color: var(--success-600);
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--success-50);
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-full);
    font-size: 0.75rem;
}

/* Attendance Time Badge */
.studentBox p:last-child {
    font-size: 0.7rem;
    color: var(--gray-400);
    font-family: monospace;
}

/* Responsive Grid */
@media (min-width: 1024px) {
    .container {
        padding: 2rem;
    }
    
    .card {
        padding: 2rem;
    }
}

@media (max-width: 768px) {
    .container {
        padding: 1rem;
    }
    
    h1 {
        font-size: 1.5rem;
    }
    
    h1::before {
        font-size: 1.5rem;
    }
    
    .card {
        padding: 1.25rem;
    }
    
    button {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
    
    .camera-box {
        max-width: 100%;
    }
    
    .studentBox {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 480px) {
    button {
        width: 100%;
        justify-content: center;
        margin-bottom: 0.5rem;
    }
    
    .button-group {
        display: flex;
        flex-direction: column;
    }
    
    #photoContainer {
        grid-template-columns: repeat(auto-fill, minmax(70px, 70px));
    }
    
    .photoBox {
        width: 70px;
        height: 70px;
    }
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--gray-100);
    border-radius: var(--radius-full);
}

::-webkit-scrollbar-thumb {
    background: var(--primary-400);
    border-radius: var(--radius-full);
}

::-webkit-scrollbar-thumb:hover {
    background: var(--primary-500);
}

/* Loading Animation */
@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}

.loading {
    background: linear-gradient(90deg, var(--gray-200) 25%, var(--gray-100) 50%, var(--gray-200) 75%);
    background-size: 1000px 100%;
    animation: shimmer 2s infinite;
}

/* Attendance List Container */
#attendanceList {
    max-height: 500px;
    overflow-y: auto;
    padding-right: 0.5rem;
}

/* Empty State */
#attendanceList:empty::before {
    content: 'No attendance records yet';
    display: block;
    text-align: center;
    padding: 2rem;
    color: var(--gray-400);
    font-size: 0.875rem;
}

/* Focus States for Accessibility */
button:focus-visible,
select:focus-visible,
.photoBox:focus-visible {
    outline: 2px solid var(--primary-500);
    outline-offset: 2px;
}

/* Smooth Transitions */
* {
    transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>

</head>

<body>

<div class="container">

<h1>AI Face Attendance System</h1>

<div class="card">

<h2>Register Student Face</h2>

<select id="studentSelect">

<option value="">
Select Student
</option>

<?php

$students = $conn->query("
SELECT * FROM students26
");

while($row = $students->fetch_assoc()){

?>

<option value="<?php echo $row['id']; ?>">

<?php echo $row['name']; ?>

-

<?php echo $row['enrollment_id']; ?>

</option>

<?php } ?>

</select>

<button onclick="startRegisterCamera()">
Start Camera
</button>

<button onclick="captureFace()">
Capture Photo
</button>

<button onclick="saveAllFaces()">
Save Registration
</button>

<div class="status" id="registerStatus"></div>

<div class="camera-box">

<video
id="registerVideo"
width="640"
height="480"
autoplay
muted
playsinline>
</video>

<canvas id="registerCanvas"></canvas>

</div>

<div id="photoContainer"></div>

</div>
<!--
<div class="card">

<h2>Take Attendance</h2>

<button onclick="startAttendanceCamera()">
Start Attendance Camera
</button>

<div class="status" id="attendanceStatus"></div>

<div class="camera-box">

<video
id="attendanceVideo"
width="640"
height="480"
autoplay
muted
playsinline>
</video>

<canvas id="attendanceCanvas"></canvas>

</div>

</div>
-->

<script>

const MODEL_URL =
'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';

let modelsLoaded = false;

let faceMatcher;

let registerStream;

let attendanceStream;

let capturedDescriptors = [];

let capturedImages = [];

async function loadModels(){

    try{

        document.getElementById(
            'registerStatus'
        ).innerHTML =
        'Loading AI Models...';

        await Promise.all([

            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),

            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),

            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)

        ]);

        modelsLoaded = true;

        document.getElementById(
            'registerStatus'
        ).innerHTML =
        'AI Models Loaded Successfully';

        console.log("MODELS LOADED");

        await loadFaces();

        loadAttendance();

    }catch(error){

        console.log(error);

        alert("Model Loading Failed");

    }

}

window.addEventListener(
    'load',
    async()=>{

        if(typeof faceapi === 'undefined'){

            alert(
                "Face API Failed To Load"
            );

            return;

        }

        await loadModels();

    }
);

async function startRegisterCamera(){

    if(!modelsLoaded){

        alert("Models Still Loading");

        return;

    }

    try{

        registerStream =
        await navigator.mediaDevices.getUserMedia({

            video:{
                width:640,
                height:480,
                facingMode:'user'
            }

        });

        const video =
        document.getElementById(
            'registerVideo'
        );

        video.srcObject =
        registerStream;

        await video.play();

        document.getElementById(
            'registerStatus'
        ).innerHTML =
        'Camera Started Successfully';

    }catch(error){

        console.log(error);

        alert("Camera Access Denied");

    }

}

async function captureFace(){

    if(!modelsLoaded){

        alert(
            "Models Still Loading"
        );

        return;

    }

    const studentId =
    document.getElementById(
        'studentSelect'
    ).value;

    if(studentId == ''){

        alert("Select Student");

        return;

    }

    const video =
    document.getElementById(
        'registerVideo'
    );

    if(video.readyState !== 4){

        alert("Camera Not Ready");

        return;

    }

    if(capturedDescriptors.length >= 5){

        alert("Maximum 5 Photos Allowed");

        return;

    }

    try{

        const detection =
        await faceapi

        .detectSingleFace(

            video,

            new faceapi.TinyFaceDetectorOptions({

                inputSize:512,
                scoreThreshold:0.3

            })

        )

        .withFaceLandmarks()

        .withFaceDescriptor();

        if(!detection){

            alert(
                "Face Not Detected Properly"
            );

            return;

        }

        capturedDescriptors.push(

            Array.from(
                detection.descriptor
            )

        );

        const canvas =
        document.createElement('canvas');

        canvas.width =
        video.videoWidth;

        canvas.height =
        video.videoHeight;

        const ctx =
        canvas.getContext('2d');

        ctx.drawImage(

            video,

            0,
            0,

            canvas.width,
            canvas.height

        );

        const image =
        canvas.toDataURL(
            'image/png'
        );

        capturedImages.push(image);

        renderCapturedPhotos();

        document.getElementById(
            'registerStatus'
        ).innerHTML =

        'Captured : ' +

        capturedDescriptors.length +

        '/5 Photos';

    }catch(error){

        console.log(error);

        alert(
            "Face Detection Failed"
        );

    }

}

function renderCapturedPhotos(){

    const container =
    document.getElementById(
        'photoContainer'
    );

    container.innerHTML = '';

    capturedImages.forEach(img=>{

        container.innerHTML += `

        <div class="photoBox">

            <img src="${img}">

        </div>

        `;

    });

}

async function saveAllFaces(){

    const studentId =
    document.getElementById(
        'studentSelect'
    ).value;

    if(studentId == ''){

        alert("Select Student");

        return;

    }

    if(capturedDescriptors.length < 3){

        alert(
            "Capture Minimum 3 Photos"
        );

        return;

    }

    for(const descriptor of capturedDescriptors){

        await fetch(

            'save_face.php',

            {

                method:'POST',

                headers:{
                    'Content-Type':'application/json'
                },

                body:JSON.stringify({

                    student_id:studentId,

                    descriptor:descriptor

                })

            }

        );

    }

    alert(
        "Face Registration Completed"
    );

    capturedDescriptors = [];

    capturedImages = [];

    document.getElementById(
        'photoContainer'
    ).innerHTML = '';

    document.getElementById(
        'registerStatus'
    ).innerHTML =
    'Registration Saved Successfully';

    await loadFaces();

}

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

    if(labeled.length > 0){

        faceMatcher =
        new faceapi.FaceMatcher(
            labeled,
            0.5
        );

    }

}

async function startAttendanceCamera(){

    if(!modelsLoaded){

        alert(
            "Models Still Loading"
        );

        return;

    }

    try{

        attendanceStream =
        await navigator.mediaDevices.getUserMedia({

            video:true

        });

        const video =
        document.getElementById(
            'attendanceVideo'
        );

        video.srcObject =
        attendanceStream;

        await video.play();

        document.getElementById(
            'attendanceStatus'
        ).innerHTML =
        'Attendance Camera Started';

        detectAttendance();

    }catch(error){

        console.log(error);

        alert("Camera Access Denied");

    }

}

async function detectAttendance(){

    if(!modelsLoaded){

        return;

    }

    const video =
    document.getElementById(
        'attendanceVideo'
    );

    const canvas =
    document.getElementById(
        'attendanceCanvas'
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

                loadAttendance();

            }

        });

    },3000);

}

async function loadAttendance(){

    const response =
    await fetch(
        'get_attendance.php'
    );

    const data =
    await response.json();

    const attendanceList =
    document.getElementById(
        'attendanceList'
    );

    attendanceList.innerHTML = '';

    data.forEach(item=>{

        attendanceList.innerHTML += `

        <div class="studentBox">

            <h3 class="present">

                ${item.name}

            </h3>

            <p>

                Enrollment :
                ${item.enrollment_id}

            </p>

            <p>

                Time :
                ${item.attendance_time}

            </p>

        </div>

        `;

    });

}

</script>

</body>
</html>