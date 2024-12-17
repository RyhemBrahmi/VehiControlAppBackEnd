<?php
// Connexion à la base de données
$host = "localhost";
$username = "root";
$password = "";
$dbname = "vehicontrol"; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $e->getMessage()
    ]);
    exit;
}

// Récupérer les données envoyées
$data = json_decode(file_get_contents("php://input"));

// Vérifier si les données nécessaires sont présentes
if (!empty($data->email) && !empty($data->password)) {
    $email = $data->email;
    $password = $data->password;

    try {
        // Préparer la requête pour vérifier l'utilisateur
        $sql = "SELECT id, email, password FROM users WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);

        // Lier les paramètres à la requête
        $stmt->bindParam(':email', $email);

        // Exécuter la requête
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Comparer directement le mot de passe en clair
            if ($password === $user['password']) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Login successful",
                    "user_id" => $user['id']
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Invalid password"
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "User not found"
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            "status" => "error",
            "message" => "SQL error: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Please provide both email and password"
    ]);
}
?>
