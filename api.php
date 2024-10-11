<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Database connection (reuse for both scripts)
require 'db_conn.php';
// Haal de HTTP-methode op die door de client wordt gebruikt (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            // Haal gebruikers en todos op

            $queryTodos = "SELECT * FROM todos ORDER BY id ";
            $resultTodos = $conn->query($queryTodos);
            $todosData = $resultTodos->fetchAll(PDO::FETCH_ASSOC);
            $responseData = array(
                "todos" => $todosData
            );
            echo json_encode($responseData); // Geef gebruikers en todos terug in JSON-formaat
            break;

        case 'POST':
            // Voeg een nieuw to-do item toe
            $title = $_POST['title'] ?? '';
            if (!empty($title)) {
                $stmt = $conn->prepare("INSERT INTO todos (title) VALUES (:title)");
                $stmt->execute(['title' => $title]);
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Title is required']);
            }
            break;

        case 'PUT':
            // Update een bestaand to-do item
            parse_str(file_get_contents("php://input"), $_PUT);
            $id = $_PUT['id'] ?? null;
            if ($id) {
                $stmt = $conn->prepare("UPDATE todos SET checked = !checked WHERE id = :id");
                $stmt->execute(['id' => $id]);
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID is required']);
            }
            break;

        case 'DELETE':
            // Verwijder een to-do item
            parse_str(file_get_contents("php://input"), $_DELETE);
            $id = $_DELETE['id'] ?? null;
            if ($id) {
                $stmt = $conn->prepare("DELETE FROM todos WHERE id = :id");
                $stmt->execute(['id' => $id]);
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID is required']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            break;
    }


?>
