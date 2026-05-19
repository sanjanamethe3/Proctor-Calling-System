<?php
/**
 * Parent Calls API
 * Handles parent call logging
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require '../config/database.php';

class ParentCallAPI {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Log Parent Call
     */
    public function logCall($data) {
        $callData = [
            'student_id' => $data['student_id'] ?? null,
            'parent_mobile' => $data['parent_mobile'] ?? null,
            'call_duration' => $data['call_duration'] ?? 0,
            'call_status' => $data['call_status'] ?? 'Not Connected',
            'remarks' => $data['remarks'] ?? '',
            'proctor_id' => $data['proctor_id'] ?? null,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ];
        
        return $this->db->insert('parent_calls', $callData);
    }
    
    /**
     * Get Call History
     */
    public function getCallHistory($filters = []) {
        $calls = $this->db->find('parent_calls', $filters);
        return [
            'success' => true,
            'data' => $calls,
            'count' => count($calls)
        ];
    }
    
    /**
     * Update Call
     */
    public function updateCall($id, $data) {
        try {
            $updateData = ['updated_at' => new MongoDB\BSON\UTCDateTime()];
            if (isset($data['call_status'])) $updateData['call_status'] = $data['call_status'];
            if (isset($data['remarks'])) $updateData['remarks'] = $data['remarks'];
            
            return $this->db->update('parent_calls', [
                '_id' => new MongoDB\BSON\ObjectId($id)
            ], $updateData);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid call ID'];
        }
    }
}

try {
    $api = new ParentCallAPI();
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($method) {
        case 'GET':
            $filters = [];
            if (isset($_GET['student_id'])) $filters['student_id'] = $_GET['student_id'];
            if (isset($_GET['date'])) $filters['date'] = $_GET['date'];
            echo json_encode($api->getCallHistory($filters));
            break;
            
        case 'POST':
            echo json_encode($api->logCall($input));
            break;
            
        case 'PUT':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID required']);
            } else {
                echo json_encode($api->updateCall($id, $input));
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
