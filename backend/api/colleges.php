<?php
/**
 * Colleges API
 * Handles college operations (CRUD)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require '../config/database.php';

class CollegeAPI {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Get All Colleges
     */
    public function getAll() {
        $colleges = $this->db->find('colleges', []);
        return [
            'success' => true,
            'data' => $colleges,
            'count' => count($colleges)
        ];
    }
    
    /**
     * Get College by ID
     */
    public function getById($id) {
        try {
            $college = $this->db->findOne('colleges', [
                '_id' => new MongoDB\BSON\ObjectId($id)
            ]);
            
            if (!$college) {
                return ['success' => false, 'message' => 'College not found'];
            }
            
            return ['success' => true, 'data' => $college];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid college ID'];
        }
    }
    
    /**
     * Create College
     */
    public function create($data) {
        $collegeData = [
            'name' => $data['name'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'email' => $data['email'] ?? null,
            'website' => $data['website'] ?? null,
            'address' => $data['address'] ?? null,
            'photo_url' => $data['photo_url'] ?? null,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ];
        
        return $this->db->insert('colleges', $collegeData);
    }
    
    /**
     * Update College
     */
    public function update($id, $data) {
        try {
            $updateData = [
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ];
            
            if (isset($data['name'])) $updateData['name'] = $data['name'];
            if (isset($data['mobile'])) $updateData['mobile'] = $data['mobile'];
            if (isset($data['email'])) $updateData['email'] = $data['email'];
            if (isset($data['website'])) $updateData['website'] = $data['website'];
            if (isset($data['address'])) $updateData['address'] = $data['address'];
            if (isset($data['photo_url'])) $updateData['photo_url'] = $data['photo_url'];
            
            return $this->db->update('colleges', [
                '_id' => new MongoDB\BSON\ObjectId($id)
            ], $updateData);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid college ID'];
        }
    }
    
    /**
     * Delete College
     */
    public function delete($id) {
        try {
            return $this->db->delete('colleges', [
                '_id' => new MongoDB\BSON\ObjectId($id)
            ]);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid college ID'];
        }
    }
}

// Handle API Request
try {
    $collegeAPI = new CollegeAPI();
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($method) {
        case 'GET':
            $id = $_GET['id'] ?? null;
            if ($id) {
                echo json_encode($collegeAPI->getById($id));
            } else {
                echo json_encode($collegeAPI->getAll());
            }
            break;
            
        case 'POST':
            echo json_encode($collegeAPI->create($input));
            break;
            
        case 'PUT':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'College ID required']);
            } else {
                echo json_encode($collegeAPI->update($id, $input));
            }
            break;
            
        case 'DELETE':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'College ID required']);
            } else {
                echo json_encode($collegeAPI->delete($id));
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
