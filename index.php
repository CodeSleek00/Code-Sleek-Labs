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
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #1e293b;
    min-height: 100vh;
    padding: 20px;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
}

/* Header Section (70% White, 20% Blue, 10% Accent) */
h1 {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 70%);
    color: #1e3a8a;
    padding: 30px;
    border-radius: 20px;
    margin-bottom: 30px;
    text-align: center;
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: -0.5px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(59,130,246,0.2);
    position: relative;
    overflow: hidden;
}

h1::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 100%);
}

h2 {
    color: #1e3a8a;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 3px solid #3b82f6;
    display: inline-block;
}

/* Cards - White Section (70%) */
.card {
    background: linear-gradient(135deg, #ffffff 0%, #fefefe 70%);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    border: 1px solid rgba(59,130,246,0.15);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 70px rgba(0,0,0,0.2);
}

/* Blue Elements (20%) */
button {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 50px;
    cursor: pointer;
    margin-right: 12px;
    margin-top: 10px;
    font-size: 14px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(59,130,246,0.3);
    letter-spacing: 0.5px;
}

button:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59,130,246,0.4);
}

button:active {
    transform: translateY(0);
}

/* Accent Elements (10% - Purple/Pink) */
button:nth-child(2) {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    box-shadow: 0 4px 15px rgba(139,92,246,0.3);
}

button:nth-child(3) {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    box-shadow: 0 4px 15px rgba(6,182,212,0.3);
}

.status {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    color: #1e40af;
    padding: 12px 20px;
    border-radius: 12px;
    margin-top: 15px;
    font-size: 13px;
    font-weight: 500;
    border-left: 4px solid #3b82f6;
}

/* Select Dropdown */
select {
    width: 100%;
    padding: 14px 18px;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    background: white;
    margin-top: 10px;
    margin-bottom: 20px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 500;
    color: #1e293b;
    transition: all 0.3s ease;
    cursor: pointer;
}

select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}

/* Circular Camera Screens */
.camera-box {
    position: relative;
    width: 100%;
    max-width: 640px;
    margin: 20px auto;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    background: #0f172a;
    aspect-ratio: 1 / 1;
    cursor: pointer;
    transition: all 0.3s ease;
}

.camera-box:hover {
    transform: scale(1.02);
    box-shadow: 0 30px 60px rgba(0,0,0,0.3);
}

video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    background: #0f172a;
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

/* Photo Container */
#photoContainer {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 25px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 16px;
    justify-content: center;
}

.photoBox {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #3b82f6;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    cursor: pointer;
}

.photoBox:hover {
    transform: scale(1.1);
    border-color: #8b5cf6;
}

.photoBox img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Student List - Modern Cards */
.studentBox {
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    padding: 18px 22px;
    border-radius: 16px;
    margin-bottom: 12px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.studentBox::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
}

.studentBox:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border-color: #cbd5e1;
}

.studentBox h3 {
    color: #1e3a8a;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.present {
    color: #059669;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
}

.present::before {
    content: '✓';
    display: inline-block;
    width: 20px;
    height: 20px;
    background: #10b981;
    color: white;
    border-radius: 50%;
    text-align: center;
    line-height: 20px;
    font-size: 12px;
}

.studentBox p {
    color: #475569;
    font-size: 13px;
    font-weight: 500;
    margin: 5px 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    body {
        padding: 15px;
    }
    
    h1 {
        font-size: 1.5rem;
        padding: 20px;
    }
    
    h2 {
        font-size: 1.2rem;
    }
    
    .card {
        padding: 20px;
    }
    
    button {
        padding: 10px 18px;
        font-size: 12px;
        margin-right: 8px;
    }
    
    .camera-box {
        max-width: 100%;
    }
    
    .photoBox {
        width: 70px;
        height: 70px;
    }
}

@media (max-width: 480px) {
    button {
        display: block;
        width: 100%;
        margin-bottom: 10px;
        margin-right: 0;
    }
    
    .studentBox {
        padding: 14px 18px;
    }
}

/* Animation for camera status */
@keyframes pulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(59,130,246,0.4);
    }
    50% {
        box-shadow: 0 0 0 15px rgba(59,130,246,0);
    }
}

.camera-box.active {
    animation: pulse 2s infinite;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 10px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
}

/* Attendance List Container */
#attendanceList {
    max-height: 400px;
    overflow-y: auto;
    padding: 5px;
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