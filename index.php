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

body{
    margin:0;
    padding:0;
    background:#0f172a;
    font-family:Arial;
    color:white;
}

.container{
    width:95%;
    margin:auto;
    padding:20px;
}

.card{
    background:#1e293b;
    padding:20px;
    border-radius:15px;
    margin-bottom:20px;
}

h1,h2{
    margin-top:0;
}

button{
    background:#38bdf8;
    color:white;
    border:none;
    padding:12px 20px;
    border-radius:10px;
    cursor:pointer;
    margin-right:10px;
    margin-top:10px;
    font-size:15px;
}

button:hover{
    background:#0ea5e9;
}

select{
    width:100%;
    padding:12px;
    border-radius:10px;
    border:none;
    margin-top:10px;
    margin-bottom:10px;
}

.camera-box{
    position:relative;
    width:100%;
    max-width:640px;
}

video{
    width:100%;
    border-radius:15px;
    background:black;
}

canvas{
    position:absolute;
    top:0;
    left:0;
}

#photoContainer{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:20px;
}

.photoBox{
    width:100px;
    height:100px;
    border-radius:10px;
    overflow:hidden;
    border:2px solid #38bdf8;
}

.photoBox img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.studentBox{
    background:#334155;
    padding:15px;
    border-radius:10px;
    margin-bottom:10px;
}

.present{
    color:#22c55e;
}

.status{
    margin-top:10px;
    font-size:14px;
    color:#38bdf8;
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