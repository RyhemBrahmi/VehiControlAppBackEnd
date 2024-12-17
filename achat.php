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

// Vérifier si les données sont envoyées correctement
if (!empty($data) && isset($data->montant) && isset($data->quantite)) {
    $montant = $data->montant;
    $quantite = $data->quantite;
    $photoFacture = $data->photoFacture ?? null;  // Le chemin de la photo (si disponible)

    try {
        // Préparer la requête d'insertion
        $sql = "INSERT INTO achat (montant, quantite, photo_facture) 
                VALUES (:montant, :quantite, :photo_facture)";
        $stmt = $pdo->prepare($sql);

        // Lier les paramètres à la requête
        $stmt->bindParam(':montant', $montant);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':photo_facture', $photoFacture);

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
        "message" => "Missing required fields."
    ]);
}
?>
