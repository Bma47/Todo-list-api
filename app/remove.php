<?php
// Controleer of er een 'id' is verzonden via het POST-verzoek
if (isset($_POST['id'])) {
    // Verbind met de database
    require '../db_conn.php';

    // Haal het ID van het to-do item op uit het POST-verzoek
    $id = $_POST['id'];

    // Controleer of het ID niet leeg is
    if (empty($id)) {
        echo 0; // Geef 0 terug als het ID leeg is
    } else {
        // Bereid een SQL-query voor om het to-do item te verwijderen op basis van het ID
        $stmt = $conn->prepare("DELETE FROM todos WHERE id=?");
        $res = $stmt->execute([$id]);

        // Controleer of de verwijdering succesvol was
        if ($res) {
            // Controleer of er nog items in de database zijn
            $stmt = $conn->query("SELECT COUNT(*) FROM todos");
            $count = $stmt->fetchColumn();

            // Reset de auto-increment ID als er geen items meer zijn
            if ($count == 0) {
                $conn->query("ALTER TABLE todos AUTO_INCREMENT = 1");
            }

            echo 1; // Geef 1 terug als de verwijdering succesvol was
        } else {
            echo 0; // Geef 0 terug als de verwijdering niet succesvol was
        }
        // Sluit de databaseverbinding
        $conn = null;
        exit();
    }
} else {
    // Als er geen ID is verzonden, stuur de gebruiker terug naar de indexpagina met een foutmelding
    header("Location: ../index.php?mess=error");
}
