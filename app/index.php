<?php

require 'vendor/autoload.php';
putenv("GRPC_TRACE=http");
putenv("GRPC_VERBOSITY=debug");
use Google\Cloud\Firestore\FirestoreClient;

class FirestoreDatabase {
    private static $instance = null;
    private $firestoreClient;

    private function __construct() {
        $this->firestoreClient = new FirestoreClient([
            'projectId' => 'grpc-php-tvc-learning',
            'keyFilePath' => '/var/www/html/application_default_credentials.json',
        ]);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new FirestoreDatabase();
        }
        return self::$instance;
    }

    public function getFirestoreClient() {
        return $this->firestoreClient;
    }
}

// Dummy query values
// $date = '2025-05-08';
$name = 'John';

try {
    echo "Initializing Firestore client...\n";
    $firestoreClient = FirestoreDatabase::getInstance()->getFirestoreClient();

    // Simulate Config constant
    $collectionId = 'schedules'; // or Config::$config['Firebase']['firestoreCollectionId']

    $collectionRef = $firestoreClient->collection($collectionId);

    $query = $collectionRef
        // ->where('date', '=', $date)
        ->where('name', '=', $name);
    
    print_r($query);

    echo "Running query on '$collectionId' where name = $name...\n";

    $documents = $query->documents();

    // foreach ($documents as $doc) {
    //     $data = $doc->data();
    //     // echo "    Date: " . $data['date'] . "\n";
    //     echo "    name: " . $data['name'] . "\n";
    // }
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
