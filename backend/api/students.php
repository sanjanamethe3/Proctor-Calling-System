<?php
/**
 * Students API
 * Handles student operations (CRUD)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require '../config/database.php';

class StudentAPI {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Get All Students
     */
    public function getAll($filters = []) {
        $students = $this->db->find('students', $filters);
        return [
            'success' => true,
            'data' => $students,
            'count' => count($students)
        ];
    }
    
    /**
     * Create Student
     */
    public function create($data) {
        $studentData = [
            'name' => $data['name'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'parent_mobile' => $data['parent_mobile'] ?? null,
            'gender' => $data['gender'] ?? null,
            'college_id' => $data['college_id'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'semester' => $data['semester'] ?? null,
            'division' => $data['division'] ?? null,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ];
        
        return $this->db->insert('students', $studentData);
    }
    
    /**
     * Update Student
     */
    public function update($id, $data) {
        try {
            $updateData = [
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ];
            
            if (isset($data['name'])) $updateData['name'] = $data['name'];
            if (isset($data['mobile'])) $updateData['mobile'] = $data['mobile'];
            if (isset($data['parent_mobile'])) $updateData['parent_mobile'] = $data['parent_mobile'];
            if (isset($data['gender'])) $updateData['gender'] = $data['gender'];
            if (isset($data['semester'])) $updateData['semester'] = $data['semester'];
            
            return $this->db->update('students', [
                '_id' => new MongoDB\BSON\ObjectId($id)
            ], $updateData);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid student ID'];
        }
    }
    
    /**
     * Delete Student
     */
    public function delete($id) {
        try {
            return $this->db->delete('students', [
                '_id' => new MongoDB\BSON\ObjectId($id)
            ]);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid student ID'];
        }
    }
}

// Handle API Request
try {
    $studentAPI = new StudentAPI();
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($method) {
        case 'GET':
            $filters = [];
            if (isset($_GET['college_id'])) $filters['college_id'] = $_GET['college_id'];
            if (isset($_GET['department_id'])) $filters['department_id'] = $_GET['department_id'];
            if (isset($_GET['semester'])) $filters['semester'] = $_GET['semester'];
            
            echo json_encode($studentAPI->getAll($filters));
            break;
            
        case 'POST':
            echo json_encode($studentAPI->create($input));
            break;
            
        case 'PUT':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Student ID required']);
            } else {
                echo json_encode($studentAPI->update($id, $input));
            }
            break;
            
        case 'DELETE':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Student ID required']);
            } else {
                echo json_encode($studentAPI->delete($id));
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
