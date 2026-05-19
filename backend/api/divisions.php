<?php
/**
 * Divisions API
 * Handles division CRUD operations
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require '../config/database.php';

class DivisionAPI {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    public function getAll($departmentId = null) {
        $filter = [];
        if ($departmentId) {
            $filter['department_id'] = $departmentId;
        }
        $divisions = $this->db->find('divisions', $filter);
        return [
            'success' => true,
            'data' => $divisions,
            'count' => count($divisions)
        ];
    }
    
    public function create($data) {
        $divisionData = [
            'name' => $data['name'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ];
        
        return $this->db->insert('divisions', $divisionData);
    }
    
    public function update($id, $data) {
        try {
            $updateData = ['updated_at' => new MongoDB\BSON\UTCDateTime()];
            if (isset($data['name'])) $updateData['name'] = $data['name'];
            
            return $this->db->update('divisions', [
                '_id' => new MongoDB\BSON\ObjectId($id)
            ], $updateData);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid division ID'];
        }
    }
    
    public function delete($id) {
        try {
            return $this->db->delete('divisions', [
                '_id' => new MongoDB\BSON\ObjectId($id)
            ]);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid division ID'];
        }
    }
}

try {
    $api = new DivisionAPI();
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($method) {
        case 'GET':
            $departmentId = $_GET['department_id'] ?? null;
            echo json_encode($api->getAll($departmentId));
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
