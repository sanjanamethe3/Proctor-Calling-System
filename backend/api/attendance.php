<?php
/**
 * Attendance API
 * Handles attendance marking and retrieval
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require '../config/database.php';

class AttendanceAPI {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Mark Attendance
     */
    public function markAttendance($data) {
        $attendanceData = [
            'student_id' => $data['student_id'] ?? null,
            'subject_id' => $data['subject_id'] ?? null,
            'date' => new MongoDB\BSON\UTCDateTime(strtotime($data['date'] ?? 'now') * 1000),
            'status' => $data['status'] ?? 'absent', // present or absent
            'marked_by' => $data['marked_by'] ?? null,
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ];
        
        return $this->db->insert('attendance', $attendanceData);
    }
    
    /**
     * Get Attendance Records
     */
    public function getAttendance($filters = []) {
        $attendance = $this->db->find('attendance', $filters);
        return [
            'success' => true,
            'data' => $attendance,
            'count' => count($attendance)
        ];
    }
    
    /**
     * Get Defaulter List (< 75% attendance)
     */
    public function getDefaulters($semester, $subject_id) {
        $pipeline = [
            [
                '$match' => [
                    'subject_id' => $subject_id
                ]
            ],
            [
                '$group' => [
                    '_id' => '$student_id',
                    'total_classes' => ['$sum' => 1],
                    'present_classes' => [
                        '$sum' => [
                            '$cond' => [
                                ['$eq' => ['$status', 'present']],
                                1,
                                0
                            ]
                        ]
                    ]
                ]
            ],
            [
                '$project' => [
                    'attendance_percentage' => [
                        '$multiply' => [
                            ['$divide' => ['$present_classes', '$total_classes']],
                            100
                        ]
                    ],
                    'total_classes' => 1,
                    'present_classes' => 1
                ]
            ],
            [
                '$match' => [
                    'attendance_percentage' => ['$lt' => 75]
                ]
            ]
        ];
        
        $defaulters = $this->db->aggregate('attendance', $pipeline);
        return [
            'success' => true,
            'data' => $defaulters,
            'count' => count($defaulters)
        ];
    }
    
    /**
     * Get Attendance Statistics
     */
    public function getStatistics($filters = []) {
        $stats = $this->db->find('attendance', $filters);
        
        $total = count($stats);
        $present = 0;
        $absent = 0;
        
        foreach ($stats as $record) {
            if ($record['status'] === 'present') {
                $present++;
            } else {
                $absent++;
            }
        }
        
        return [
            'success' => true,
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0
        ];
    }
}

// Handle API Request
try {
    $attendanceAPI = new AttendanceAPI();
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($method) {
        case 'GET':
            $action = $_GET['action'] ?? 'get';
            
            if ($action === 'defaulters') {
                $semester = $_GET['semester'] ?? null;
                $subject_id = $_GET['subject_id'] ?? null;
                
                if (!$semester || !$subject_id) {
                    echo json_encode(['success' => false, 'message' => 'Semester and subject_id required']);
                } else {
                    echo json_encode($attendanceAPI->getDefaulters($semester, $subject_id));
                }
            } elseif ($action === 'statistics') {
                $filters = [];
                if (isset($_GET['student_id'])) $filters['student_id'] = $_GET['student_id'];
                if (isset($_GET['subject_id'])) $filters['subject_id'] = $_GET['subject_id'];
                
                echo json_encode($attendanceAPI->getStatistics($filters));
            } else {
                $filters = [];
                if (isset($_GET['student_id'])) $filters['student_id'] = $_GET['student_id'];
                if (isset($_GET['date'])) $filters['date'] = $_GET['date'];
                
                echo json_encode($attendanceAPI->getAttendance($filters));
            }
            break;
            
        case 'POST':
            echo json_encode($attendanceAPI->markAttendance($input));
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
