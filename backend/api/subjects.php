<?php
/**
 * Subjects API
 * Handles subject CRUD operations
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require '../config/database.php';

class SubjectAPI {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    public function getAll($departmentId = null, $semesterId = null) {
        $filter = [];
        if ($departmentId) $filter['department_id'] = $departmentId;
        if ($semesterId) $filter['semester_id'] = $semesterId;
        
        $subjects = $this->db->find('subjects', $filter);
        return [
            'success' => true,
            'data' => $subjects,
            'count' => count($subjects)
        ];
    }
    
    public function create($data) {
        $subjectData = [
            'name' => $data['name'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'semester_id' => $data['semester_id'] ?? null,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ];
        
        return $this->db->insert('subjects', $subjectData);
    }
    
    public function update($id, $data) {
        try {
            $updateData = ['updated_at' => new MongoDB\BSON\UTCDateTime()];
            if (isset($data['name'])) $updateData['name'] = $data['name'];
            
            return $this->db->update('subjects', [
                '_id' => new MongoDB\BSON\ObjectId($id)
            ], $updateData);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid subject ID'];
        }
    }
    
    public function delete($id) {
        try {
            return $this->db->delete('subjects', [
                '_id' => new MongoDB\BSON\ObjectId($id)
            ]);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid subject ID'];
        }
    }
}

try {
    $api = new SubjectAPI();
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($method) {
        case 'GET':
            $deptId = $_GET['department_id'] ?? null;
            $semId = $_GET['semester_id'] ?? null;
            echo json_encode($api->getAll($deptId, $semId));
            break;
        case 'POST':
            echo json_encode($api->create($input));
            break;
        case 'PUT':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID required']);
            } else {
                echo json_encode($api->update($id, $input));
            }
            break;
        case 'DELETE':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID required']);
            } else {
                echo json_encode($api->delete($id));
            }
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
