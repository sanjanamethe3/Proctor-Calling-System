<?php
/**
 * Departments API
 * Handles department CRUD operations
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require '../config/database.php';

class DepartmentAPI {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    public function getAll($collegeId = null) {
        $filter = [];
        if ($collegeId) {
            $filter['college_id'] = $collegeId;
        }
        $departments = $this->db->find('departments', $filter);
        return [
            'success' => true,
            'data' => $departments,
            'count' => count($departments)
        ];
    }
    
    public function create($data) {
        $departmentData = [
            'name' => $data['name'] ?? null,
            'college_id' => $data['college_id'] ?? null,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ];
        
        return $this->db->insert('departments', $departmentData);
    }
    
    public function update($id, $data) {
        try {
            $updateData = ['updated_at' => new MongoDB\BSON\UTCDateTime()];
            if (isset($data['name'])) $updateData['name'] = $data['name'];
            
            return $this->db->update('departments', [
                '_id' => new MongoDB\BSON\ObjectId($id)
            ], $updateData);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid department ID'];
        }
    }
    
    public function delete($id) {
        try {
            return $this->db->delete('departments', [
                '_id' => new MongoDB\BSON\ObjectId($id)
            ]);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid department ID'];
        }
    }
}

try {
    $api = new DepartmentAPI();
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($method) {
        case 'GET':
            $collegeId = $_GET['college_id'] ?? null;
            echo json_encode($api->getAll($collegeId));
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
