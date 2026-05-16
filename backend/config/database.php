<?php
/**
 * MongoDB Database Configuration
 * Connect to MongoDB and manage database operations
 */

require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\Collection;

class Database {
    private $client;
    private $database;
    
    public function __construct() {
        try {
            // MongoDB Connection URI
            $mongoUri = 'mongodb://localhost:27017';
            
            // Create MongoDB Client
            $this->client = new Client($mongoUri);
            
            // Select Database
            $this->database = $this->client->proctor_calling_system;
            
            // Test connection
            $this->database->command(['ping' => 1]);
            
        } catch (Exception $e) {
            die('Database Connection Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Get Database Instance
     */
    public function getDatabase() {
        return $this->database;
    }
    
    /**
     * Get Collection
     */
    public function getCollection($collectionName) {
        return $this->database->$collectionName;
    }
    
    /**
     * Insert Document
     */
    public function insert($collection, $data) {
        try {
            $result = $this->getCollection($collection)->insertOne($data);
            return [
                'success' => true,
                'id' => (string)$result->getInsertedId(),
                'message' => 'Record inserted successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error inserting record: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Find Documents
     */
    public function find($collection, $filter = []) {
        try {
            $cursor = $this->getCollection($collection)->find($filter);
            $result = [];
            foreach ($cursor as $document) {
                $result[] = $document;
            }
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Find One Document
     */
    public function findOne($collection, $filter) {
        try {
            return $this->getCollection($collection)->findOne($filter);
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Update Document
     */
    public function update($collection, $filter, $update) {
        try {
            $result = $this->getCollection($collection)->updateOne(
                $filter,
                ['$set' => $update]
            );
            return [
                'success' => true,
                'modified' => $result->getModifiedCount(),
                'message' => 'Record updated successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error updating record: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Delete Document
     */
    public function delete($collection, $filter) {
        try {
            $result = $this->getCollection($collection)->deleteOne($filter);
            return [
                'success' => true,
                'deleted' => $result->getDeletedCount(),
                'message' => 'Record deleted successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error deleting record: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Count Documents
     */
    public function count($collection, $filter = []) {
        try {
            return $this->getCollection($collection)->countDocuments($filter);
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get Aggregation Results
     */
    public function aggregate($collection, $pipeline) {
        try {
            $cursor = $this->getCollection($collection)->aggregate($pipeline);
            $result = [];
            foreach ($cursor as $document) {
                $result[] = $document;
            }
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }
}

// Create Database Instance
$db = new Database();
?>
