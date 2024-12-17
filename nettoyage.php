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
if (!empty($data)) {
    $photoBefore = $data->photo_before ?? null;  // Le chemin de la photo (si disponible)
    $videoBefore = $data->video_before ?? null;  // Le chemin de la vidéo (si disponible)
    $photoAfter = $data->photo_after?? null; 
    $videoAfter = $data->video_after?? null; 

    
    try {
        // Préparer la requête d'insertion
        $sql = "INSERT INTO nettoyage (photo_before, video_before, photo_after, video_after) 
                VALUES (:photo_before, :video_before, :photo_after, :video_after)";
        $stmt = $pdo->prepare($sql);

        // Lier les paramètres à la requête
        $stmt->bindParam(':photo_before', $photoBefore);
        $stmt->bindParam(':video_before', $videoBefore);
        $stmt->bindParam(':photo_after', $photoAfter);
        $stmt->bindParam(':video_after', $videoAfter);

        // Exécuter la requête
        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Data saved successfully."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to save data."
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
        "message" => "Missing required fields. Please provide 'photo_before', 'video_before', 'photo_after', and 'video_after'."
    ]);
}
?>
