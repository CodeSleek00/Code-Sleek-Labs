<?php
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>AI Face Attendance System</title>
<script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#0f172a;
    color:white;
    font-family:Arial, sans-serif;
}

header{
    padding:20px;
    background:#111827;
    border-bottom:1px solid #1e293b;
}

header h1{
    color:#38bdf8;
}

.container{
    width:95%;
    margin:auto;
    padding:20px;
}

.card{
    background:#111827;
    border:1px solid #1e293b;
    border-radius:12px;
    padding:20px;
    margin-bottom:20px;
}

.card h2{
    margin-bottom:20px;
    color:#38bdf8;
}

table{
    width:100%;
    border-collapse:collapse;
}

table th,
table td{
    padding:12px;
    border-bottom:1px solid #1e293b;
    text-align:left;
}

table th{
    background:#1e293b;
}

button{
    background:#38bdf8;
    color:white;
    border:none;
    padding:12px 20px;
    border-radius:8px;
    cursor:pointer;
    margin-top:10px;
}

button:hover{
    background:#0ea5e9;
}

select{
    width:100%;
    padding:12px;
    background:#0f172a;
    border:1px solid #334155;
    border-radius:8px;
    color:white;
    margin-bottom:15px;
}

.camera-box{
    position:relative;
    width:100%;
    max-width:700px;
}

video{
    width:100%;
    border-radius:12px;
}

canvas{
    position:absolute;
    top:0;
    left:0;
}

.status{
    margin-top:20px;
    padding:15px;
    border-radius:10px;
    display:none;
}

.success{
    background:#14532d;
    color:#4ade80;
}

.error{
    background:#450a0a;
    color:#f87171;
}

.log-item{
    padding:15px;
    border-bottom:1px solid #1e293b;
}

.badge{
    background:#14532d;
    color:#4ade80;
    padding:5px 10px;
    border-radius:20px;
    font-size:12px;
}

</style>

</head>

<body>

<header>
    <h1>AI Face Attendance System</h1>
</header>

<div class="container">

    <!-- STUDENTS -->

    <div class="card">

        <h2>Students Database</h2>

        <table>

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Enrollment Number</th>
                    <th>Class</th>
                </tr>

            </thead>

            <tbody>

            <?php

            $students = $conn->query("
            SELECT * FROM students26
            ");

            while($row = $students->fetch_assoc()){

            ?>

            <tr>

                <td>
                    <?php echo $row['id']; ?>
                </td>

                <td>
                    <?php echo $row['name']; ?>
                </td>

                <td>
                    <?php echo $row['enrollment_id']; ?>
                </td>

                <td>
                    <?php echo $row['class']; ?>
                </td>

            </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

    <!-- FACE REGISTRATION -->

    <div class="card">

        <h2>Register Student Face</h2>

        <select id="studentSelect">

            <option value="">
                Select Student
            </option>

            <?php

            $students2 = $conn->query("
            SELECT * FROM students26
            ");

            while($row2 = $students2->fetch_assoc()){

            ?>

            <option value="<?php echo $row2['id']; ?>">

                <?php echo $row2['name']; ?>

                -

                <?php echo $row2['enrollment_id']; ?>

            </option>

            <?php } ?>

        </select>

        <button onclick="startRegisterCamera()">
            Start Camera
        </button>

        <br><br>

        <div class="camera-box">

            <video
            id="registerVideo"
            autoplay
            muted
            playsinline>
            </video>

            <canvas id="registerCanvas"></canvas>

        </div>

        <button onclick="captureFace()">
            Capture Face
        </button>

    </div>

    <!-- ATTENDANCE -->

    <div class="card">

        <h2>Take Attendance</h2>

        <button onclick="startAttendance()">
            Start Attendance Camera
        </button>

        <br><br>

        <div class="camera-box">

            <video
            id="video"
            autoplay
            muted
            playsinline>
            </video>

            <canvas id="overlay"></canvas>

        </div>

        <div id="statusBox" class="status"></div>

    </div>

    <!-- ATTENDANCE LOG -->

    <div class="card">

        <h2>Today's Attendance</h2>

        <div id="attendanceLog"></div>

    </div>

</div>

<script>

const MODEL_URL =
'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';

let faceMatcher = null;

let attendanceRunning = false;

let markedStudents = [];


// LOAD MODELS

async function loadModels(){

    try{

        await Promise.all([

            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),

            faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL),

            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)

        ]);

        await loadFaces();

        alert("AI Models Loaded Successfully");

    }catch(error){

        console.log(error);

        alert("Model Loading Failed");

    }

}

loadModels();


// LOAD FACES

async function loadFaces(){

    const response =
    await fetch('load_faces.php');

    const data =
    await response.json();

    const labeled = [];

    for(const item of data){

        const descriptors = [

            new Float32Array(
                JSON.parse(item.descriptor)
            )

        ];

        labeled.push(

            new faceapi.LabeledFaceDescriptors(

                item.student_id.toString(),

                descriptors

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


// REGISTER CAMERA

async function startRegisterCamera(){

    try{

        const video =
        document.getElementById(
            'registerVideo'
        );

        const stream =
        await navigator.mediaDevices.getUserMedia({

            video:{
                width:640,
                height:480,
                facingMode:"user"
            },

            audio:false

        });

        video.srcObject = stream;

        await video.play();

        alert("Camera Started");

    }catch(error){

        console.log(error);

        alert("Camera Permission Denied");

    }

}


// CAPTURE FACE

async function captureFace(){

    const studentId =
    document.getElementById(
        'studentSelect'
    ).value;

    if(studentId == ''){

        alert("Please Select Student");

        return;

    }

    const video =
    document.getElementById(
        'registerVideo'
    );

    const detection =
    await faceapi

    .detectSingleFace(

        video,

        new faceapi.TinyFaceDetectorOptions({

            inputSize:320,
            scoreThreshold:0.5

        })

    )

    .withFaceLandmarks()

    .withFaceDescriptor();

    if(!detection){

        alert("Face Not Detected");

        return;

    }

    const descriptor =
    Array.from(
        detection.descriptor
    );

    const response =
    await fetch('save_face.php',{

        method:'POST',

        headers:{
            'Content-Type':'application/json'
        },

        body:JSON.stringify({

            student_id:studentId,

            descriptor:descriptor

        })

    });

    const result =
    await response.json();

    if(result.status == 'success'){

        alert("Face Registered Successfully");

        await loadFaces();

    }else{

        alert("Registration Failed");

    }

}


// START ATTENDANCE

async function startAttendance(){

    if(faceMatcher == null){

        alert("No Registered Faces");

        return;

    }

    attendanceRunning = true;

    const video =
    document.getElementById('video');

    const stream =
    await navigator.mediaDevices.getUserMedia({

        video:{
            width:640,
            height:480,
            facingMode:"user"
        },

        audio:false

    });

    video.srcObject = stream;

    await video.play();

    detectAttendance();

}


// DETECT ATTENDANCE

async function detectAttendance(){

    const video =
    document.getElementById('video');

    const canvas =
    document.getElementById('overlay');

    canvas.width =
    video.videoWidth;

    canvas.height =
    video.videoHeight;

    const displaySize = {

        width:video.videoWidth,

        height:video.videoHeight

    };

    faceapi.matchDimensions(
        canvas,
        displaySize
    );

    while(attendanceRunning){

        const detections =
        await faceapi

        .detectAllFaces(

            video,

            new faceapi.TinyFaceDetectorOptions({

                inputSize:320,

                scoreThreshold:0.5

            })

        )

        .withFaceLandmarks()

        .withFaceDescriptors();

        const resized =
        faceapi.resizeResults(
            detections,
            displaySize
        );

        const ctx =
        canvas.getContext('2d');

        ctx.clearRect(
            0,
            0,
            canvas.width,
            canvas.height
        );

        resized.forEach(async detection => {

            const result =
            faceMatcher.findBestMatch(
                detection.descriptor
            );

            const box =
            detection.detection.box;

            let drawBox;

            if(result.label != 'unknown'){

                drawBox =
                new faceapi.draw.DrawBox(box,{

                    label:
                    'Student ID : ' +
                    result.label

                });

                await markAttendance(
                    result.label
                );

            }else{

                drawBox =
                new faceapi.draw.DrawBox(box,{

                    label:'Unknown'

                });

            }

            drawBox.draw(canvas);

        });

        await new Promise(resolve =>
            setTimeout(resolve,1000)
        );

    }

}


// MARK ATTENDANCE

async function markAttendance(studentId){

    if(markedStudents.includes(studentId)){

        return;

    }

    markedStudents.push(studentId);

    const response =
    await fetch('mark_attendance.php',{

        method:'POST',

        headers:{
            'Content-Type':'application/json'
        },

        body:JSON.stringify({

            student_id:studentId

        })

    });

    const result =
    await response.json();

    const statusBox =
    document.getElementById(
        'statusBox'
    );

    statusBox.style.display =
    'block';

    if(result.status == 'marked'){

        statusBox.className =
        'status success';

        statusBox.innerHTML =
        'Attendance Marked Successfully';

        loadAttendanceLog();

    }else{

        statusBox.className =
        'status error';

        statusBox.innerHTML =
        'Attendance Already Marked';

    }

}


// LOAD ATTENDANCE LOG

async function loadAttendanceLog(){

    const response =
    await fetch(
        'get_attendance.php'
    );

    const data =
    await response.json();

    const log =
    document.getElementById(
        'attendanceLog'
    );

    log.innerHTML = '';

    data.forEach(item => {

        log.innerHTML += `

        <div class="log-item">

            <strong>
                ${item.name}
            </strong>

            <br><br>

            ${item.enrollment_id}

            <br><br>

            <span class="badge">
                Present
            </span>

        </div>

        `;

    });

}

loadAttendanceLog();

</script>

</body>
</html>