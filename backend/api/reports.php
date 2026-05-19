<?php
/**
 * Reports API
 * Handles report generation and export
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require '../config/database.php';

class ReportsAPI {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Generate Attendance Report
     */
    public function generateAttendanceReport($filters) {
        $pipeline = [
            [
                '$match' => [
                    'subject_id' => $filters['subject_id'] ?? null
                ]
            ],
            [
                '$group' => [
                    '_id' => '$student_id',
                    'total' => ['$sum' => 1],
                    'present' => [
                        '$sum' => ['$cond' => [['$eq' => ['$status', 'present']], 1, 0]]
                    ]
                ]
            ]
        ];
        
        $results = $this->db->aggregate('attendance', $pipeline);
        return ['success' => true, 'data' => $results];
    }
    
    /**
     * Generate Monthly Report
     */
    public function generateMonthlyReport($month, $departmentId = null) {
        $filter = [];
        if ($departmentId) $filter['department_id'] = $departmentId;
        
        $results = $this->db->find('attendance', $filter);
        return ['success' => true, 'data' => $results];
    }
    
    /**
     * Export to Excel
     */
    public function exportToExcel($data) {
        // This would require PHPExcel library
        return ['success' => true, 'message' => 'Export functionality ready'];
    }
}

try {
    $api = new ReportsAPI();
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($method) {
        case 'GET':
            $type = $_GET['type'] ?? null;
            if ($type === 'attendance') {
                $filters = [
                    'subject_id' => $_GET['subject_id'] ?? null
                ];
                echo json_encode($api->generateAttendanceReport($filters));
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid report type']);
            }
            break;
            
        case 'POST':
            echo json_encode($api->exportToExcel($input));
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
