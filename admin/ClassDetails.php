<?php

include '../connection.php';

if(isset($_GET['Class']) && isset($_GET['Grade'])){
    $ClassID = $_GET['Class'];
    $Grade = $_GET['Grade'];
    
    // Handle schedule clearing if requested
    if(isset($_GET['clear']) && isset($_POST['clear'])) {
        // The form will only submit if user confirmed in JavaScript
        // First delete all schedule details
        $deleteDetails = "DELETE sd FROM s_details sd 
                         JOIN schedule s ON sd.ScheduleID = s.ScheduleID 
                         WHERE s.ClassID = $ClassID";
        if(mysqli_query($connect, $deleteDetails)) {
            // Then delete the schedule itself
            $deleteSchedule = "DELETE FROM schedule WHERE ClassID = $ClassID";
            if(mysqli_query($connect, $deleteSchedule)) {
                echo "<script>
                    alert('Schedule successfully cleared!');
                    window.location.href = 'Schedule.php?Class=" . $ClassID . "&Grade=" . $Grade . "';
                </script>";
                exit;
            } else {
                echo "<script>
                    alert('Error clearing schedule. Please try again.');
                    window.location.href = 'Schedule.php?Class=" . $ClassID . "&Grade=" . $Grade . "';
                </script>";
                exit;
            }
        } else {
            echo "<script>
                alert('Error clearing schedule details. Please try again.');
                window.location.href = 'Schedule.php?Class=" . $ClassID . "&Grade=" . $Grade . "';
            </script>";
            exit;
        }
    }
    
    // First check if a schedule exists for this class
    $selectSchedule = "SELECT * FROM schedule Where ClassID=$ClassID AND grade=$Grade";
    $runSchedule = mysqli_query($connect,$selectSchedule);  
    
    $scheduleExists = false;
    $hasAssignedPeriods = false;
    $scheduleData = array();
    
    if(mysqli_num_rows($runSchedule) > 0) {
        $fetchSchedule = mysqli_fetch_assoc($runSchedule);
        $ScheduleID = $fetchSchedule['ScheduleID'];
        $scheduleExists = true;
        
        // Check if the schedule has any assigned periods
        $schedule = "SELECT * FROM `s_details`
                    JOIN `teachers` ON `teachers`.`TeacherID` = `s_details`.`TeacherID`
                    JOIN `subjects` ON `subjects`.`SubjectID` = `s_details`.`SubjectID`
                    JOIN `schedule` ON `schedule`.`ScheduleID` = `s_details`.`ScheduleID`
                    WHERE `schedule`.`ScheduleID` = '$ScheduleID'
                    ORDER BY 
                    FIELD(Weekday, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), 
                    PeriodNumber";
        $scheduleRun = mysqli_query($connect, $schedule);

        if(mysqli_num_rows($scheduleRun) > 0) {
            $hasAssignedPeriods = true;
            
            // Organize data by day and period
            while ($row = mysqli_fetch_assoc($scheduleRun)) {
                $day = $row['Weekday'];
                $period = $row['PeriodNumber'];
                
                if (!isset($scheduleData[$day])) {
                    $scheduleData[$day] = array();
                }
                
                $scheduleData[$day][$period] = array(
                    'subject' => $row['SubjectName'],
                    'teacher' => $row['TeacherName'],
                    'teacherId' => $row['TeacherID'],
                    'start_time' => date('h:i A', strtotime($row['StartTime'])),
                    'end_time' => date('h:i A', strtotime($row['EndTime']))
                );
            }
        }
    }

    // Create new schedule if it doesn't exist and we're not already processing a form submission
    if(!$scheduleExists && !isset($_POST['Add'])) {
        $insertSchedule = "INSERT INTO schedule (ClassID, grade, Semester) 
                          VALUES ($ClassID, $Grade, '1')";
        mysqli_query($connect, $insertSchedule);
        $ScheduleID = mysqli_insert_id($connect);
        $scheduleExists = true;
    }

    // Days of the week
    $weekDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];

    // Process form submission
    if(isset($_POST['Add'])){
        $days = $_POST['day'];
        $subjects = $_POST['subject'];
        $TeachersID = $_POST['Teacher'];
        $period_numbers = $_POST['PeriodNumber'];
        $start_times = $_POST['start_time'];
        $end_times = $_POST['end_time'];

        for ($i = 0; $i < count($period_numbers); $i++) {
            $day = $days[$i];
            $subject = $subjects[$i];
            $TeacherID = $TeachersID[$i];
            $period_number = $period_numbers[$i];
            $start_time = $start_times[$i];
            $end_time = $end_times[$i];

            if (!empty($subject)) {
                $sql = "INSERT INTO s_details (DetailID, ScheduleID, Weekday, PeriodNumber, SubjectID, TeacherID, StartTime, EndTime)
                       VALUES (null, $ScheduleID, '$day', $period_number, $subject, $TeacherID, '$start_time', '$end_time')";
                mysqli_query($connect, $sql);
            }
        }
        echo "<script>window.location.href = 'Schedule.php?Class=" . $ClassID . "&Grade=" . $Grade . "';</script>";
    }
} else {
    echo "Both Class and Grade must be provided";
    exit;
}

// Get basic class information
$classQuery = "SELECT c.ClassName, c.ClassID 
               FROM class c 
               WHERE c.ClassID = $ClassID";
$classResult = mysqli_query($connect, $classQuery);
$classInfo = mysqli_fetch_assoc($classResult);

if (!$classInfo) {
    die("Class not found");
}

// Get grade information
$gradeQuery = "SELECT GradeNumber, GradeID 
               FROM grades 
               WHERE GradeID = $Grade";
$gradeResult = mysqli_query($connect, $gradeQuery);
$gradeInfo = mysqli_fetch_assoc($gradeResult);

if (!$gradeInfo) {
    die("Grade not found");
}

// Get number of subjects (only if schedule exists and has assigned periods)
if ($hasAssignedPeriods) {
    $subjectsQuery = "SELECT COUNT(DISTINCT SubjectID) as subject_count 
                      FROM s_details sd 
                      JOIN schedule s ON sd.ScheduleID = s.ScheduleID 
                      WHERE s.ClassID = $ClassID AND s.grade = $Grade";
    $subjectsResult = mysqli_query($connect, $subjectsQuery);
    $subjectsInfo = mysqli_fetch_assoc($subjectsResult);
} else {
    $subjectsInfo = array('subject_count' => 0);
}

// Get students with IDs
$studentsQuery = "SELECT StudentID, StudentName 
                 FROM students 
                 WHERE Class = $ClassID AND Grade = $Grade
                 ORDER BY StudentName";
$studentsResult = mysqli_query($connect, $studentsQuery);
$studentCount = mysqli_num_rows($studentsResult);

// Get teachers with IDs (only if schedule exists and has assigned periods)
if ($hasAssignedPeriods) {
    $teachersQuery = "SELECT DISTINCT t.TeacherID, t.TeacherName 
                     FROM teachers t 
                     JOIN s_details sd ON t.TeacherID = sd.TeacherID
                     JOIN schedule s ON sd.ScheduleID = s.ScheduleID
                     WHERE s.ClassID = $ClassID AND s.grade = $Grade";
    $teachersResult = mysqli_query($connect, $teachersQuery);
} else {
    $teachersResult = false;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/schedule.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Additional styles for student list */
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: var(--dark);
            color: white;
        }

        .students-container {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 100%;
        }
        
        .horizontal-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 15px 5px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        
        .horizontal-list.show {
            max-height: 500px;
        }
        
        .student-item {
            background: #f5f5f5;
            padding: 10px 20px;
            border-radius: 5px;
            width: 100%;
            transition: all 0.2s ease;
        }
        
        .student-item a {
            color: var(--dark);
            text-decoration: none;
            display: block;
            width: 100%;
        }
        
        .student-item:hover {
            background: var(--primary);
            transform: translateX(10px);
        }
        
        .student-item:hover a {
            color: white;
        }
        
        .toggle-list {
            width: 100%;
            text-align: left;
            padding: 12px 20px;
            background: var(--dark);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
        }
        
        .toggle-list:hover {
            background: var(--primary);
        }

        .toggle-list i {
            transition: transform 0.3s ease;
        }

        .toggle-list.active i {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="class-header">
                <h2>Class: <?php echo $gradeInfo['GradeID']; ?> <?php echo $classInfo['ClassName']; ?></h2>
                <p>Grade: <?php echo $gradeInfo['GradeID']; ?></p>
            </div>

            <div class="class-stats">
                <div class="stat-item">
                    <i class="fas fa-book"></i>
                    <span>Subjects: <?php echo $subjectsInfo['subject_count']; ?></span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <span>Students: <?php echo $studentCount; ?></span>
                </div>
            </div>

            <!-- Teachers List -->
            <button class="collapsible">
                Teacher List
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="content">
                <?php if ($teachersResult && mysqli_num_rows($teachersResult) > 0): ?>
                    <ul class="name-list">
                        <?php while($teacher = mysqli_fetch_assoc($teachersResult)) { ?>
                            <li><a href="TeacherProfile.php?id=<?php echo $teacher['TeacherID']; ?>"><?php echo htmlspecialchars($teacher['TeacherName']); ?></a></li>
                        <?php } ?>
                    </ul>
                <?php else: ?>
                    <p>No teachers assigned yet</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Back Button -->
            <a href="AllClass.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to All Classes
            </a>

            <!-- Students List (Horizontal) -->
            <div class="students-container">
                <button class="toggle-list" onclick="toggleStudentList()">
                    <span>Student List</span>
                    <i class="fas fa-chevron-down" id="student-list-icon"></i>
                </button>
                <div class="horizontal-list" id="studentList">
                    <?php if ($studentCount > 0): ?>
                        <?php mysqli_data_seek($studentsResult, 0); // Reset the pointer to start ?>
                        <?php while($student = mysqli_fetch_assoc($studentsResult)) { ?>
                            <div class="student-item">
                                <a href="StudentProfile.php?id=<?php echo $student['StudentID']; ?>"><?php echo htmlspecialchars($student['StudentName']); ?></a>
                            </div>
                        <?php } ?>
                    <?php else: ?>
                        <p>No students assigned to this class</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="schedule-container">
                <h2 class="schedule-title">Class Schedule</h2>
                
                <?php if (!$hasAssignedPeriods): ?>
                    <!-- Schedule Form when no schedule exists or no periods are assigned -->
                    <form method="post" action="Schedule.php?Class=<?php echo $ClassID; ?>&Grade=<?php echo $Grade; ?>">
                        <table class="schedule-table">
                            <tr>
                                <th>Day</th>
                                <th>Period</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                            </tr>
                            <?php foreach ($weekDays as $day){ 
                                $periodsPerDay = 7;
                                $periodTimes = [
                                    1 => ['start' => '08:00', 'end' => '08:45'],
                                    2 => ['start' => '08:45', 'end' => '09:30'],
                                    3 => ['start' => '09:30', 'end' => '10:15'],
                                    4 => ['start' => '10:15', 'end' => '11:00'],
                                    5 => ['start' => '11:30', 'end' => '12:15'],
                                    6 => ['start' => '12:15', 'end' => '13:00'],
                                    7 => ['start' => '13:00', 'end' => '13:45']
                                ];
                                
                                for($period = 1; $period <= $periodsPerDay; $period++) { ?>
                                    <tr>
                                        <?php if($period === 1) { ?>
                                            <td rowspan="<?php echo $periodsPerDay; ?>" class="day-cell"><?php echo $day; ?></td>
                                        <?php } ?>
                                        <td>
                                            <input type="number" name="PeriodNumber[]" value="<?php echo $period; ?>" readonly>
                                        </td>
                                        <td>
                                            <select name="subject[]" class="subject-select" onchange="updateTeachers(this)">
                                                <option value="">Select Subject</option>
                                                <?php
                                                $selectSubject="SELECT * FROM subjects";
                                                $runSubject=mysqli_query($connect,$selectSubject);
                                                while($fetchSubject=mysqli_fetch_assoc($runSubject)){ ?>
                                                    <option value="<?php echo $fetchSubject['SubjectID']; ?>"><?php echo $fetchSubject['SubjectName']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="Teacher[]" class="teacher-select">
                                                <option value="">Select teacher</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="time" name="start_time[]" value="<?php echo $periodTimes[$period]['start']; ?>" readonly>
                                        </td>
                                        <td>
                                            <input type="time" name="end_time[]" value="<?php echo $periodTimes[$period]['end']; ?>" readonly>
                                        </td>
                                        <input type="hidden" name="day[]" value="<?php echo $day; ?>">
                                    </tr>
                                <?php } 
                            } ?>
                        </table>
                        <button type="submit" name="Add" class="submit-btn">Save Schedule</button>
                    </form>
                <?php else: ?>
                    <!-- Display existing schedule -->
                    <div class="schedule">
                        <table class="timetable">
                            <thead>
                                <tr>
                                    <th class="day-header">Day/Period</th>
                                    <th class="period-header">Period 1<br>8:00-8:45</th>
                                    <th class="period-header">Period 2<br>8:45-9:30</th>
                                    <th class="period-header">Period 3<br>9:30-10:15</th>
                                    <th class="period-header">Period 4<br>10:15-11:00</th>
                                    <th class="break-cell">Break<br>11:00-11:30</th>
                                    <th class="period-header">Period 5<br>11:30-12:15</th>
                                    <th class="period-header">Period 6<br>12:15-1:00</th>
                                    <th class="period-header">Period 7<br>1:00-1:45</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($weekDays as $day): ?>
                                    <tr>
                                        <td class="day-header"><?php echo $day; ?></td>
                                        <?php for ($period = 1; $period <= 7; $period++): ?>
                                            <?php if ($period == 5): ?>
                                                <td class="break-cell">Break</td>
                                            <?php endif; ?>
                                            <td class="period-cell">
                                                <?php if (isset($scheduleData[$day][$period])): ?>
                                                    <div class="subject">
                                                        <?php echo htmlspecialchars($scheduleData[$day][$period]['subject']); ?>
                                                    </div>
                                                    <div class="teacher">
                                                        <a href="TeacherProfile.php?id=<?php echo $scheduleData[$day][$period]['teacherId']; ?>"> 
                                                            <?php echo htmlspecialchars($scheduleData[$day][$period]['teacher']); ?>
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="free-period">Free</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endfor; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <!-- Add button to clear schedule and create new one -->
                        <form method="post" action="Schedule.php?Class=<?php echo $ClassID; ?>&Grade=<?php echo $Grade; ?>&clear=1" style="margin-top: 20px;" id="clearScheduleForm">
                            <button type="submit" name="clear" class="btn btn-warning" onclick="return confirmClear()">Clear Schedule and Create New</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Confirmation function for clearing schedule
        function confirmClear() {
            return confirm("Warning: This will delete the entire schedule and all assigned periods. This action cannot be undone. Are you sure you want to proceed?");
        }

        // Collapsible functionality
        var coll = document.getElementsByClassName("collapsible");
        for (var i = 0; i < coll.length; i++) {
            coll[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var content = this.nextElementSibling;
                var icon = this.querySelector('i');
                
                if (content.style.display === "block") {
                    content.style.display = "none";
                    icon.className = "fas fa-chevron-down";
                } else {
                    content.style.display = "block";
                    icon.className = "fas fa-chevron-up";
                }
            });
        }

        // AJAX for teacher selection
        function updateTeachers(subjectSelect) {
            const selectedSubject = $(subjectSelect).val();
            const teacherSelect = $(subjectSelect).closest('tr').find('.teacher-select');
            
            if(selectedSubject) {
                $.ajax({
                    url: 'get_teachers.php',
                    type: 'POST',
                    data: {
                        subject_id: selectedSubject
                    },
                    success: function(response) {
                        teacherSelect.html(response);
                    },
                    error: function() {
                        alert('Error fetching teachers');
                    }
                });
            } else {
                teacherSelect.html('<option value="">Select teacher</option>');
            }
        }

        // Toggle student list functionality
        function toggleStudentList() {
            const studentList = document.getElementById('studentList');
            const icon = document.getElementById('student-list-icon');
            studentList.classList.toggle('show');
            
            if (studentList.classList.contains('show')) {
                icon.className = 'fas fa-chevron-up';
            } else {
                icon.className = 'fas fa-chevron-down';
            }
        }
    </script>
</body>
</html>
