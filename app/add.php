<?php
// Controleer of de 'title' is verzonden via het POST-verzoek
if (isset($_POST['title'])) {
    // Inclusief databaseverbinding
    require '../db_conn.php';

    // Haal de titel, vervaldatum en tijd op uit het POST-verzoek
    $title = $_POST['title'];
    $due_date = $_POST['due_date'];
    $due_time = $_POST['due_time']; // Get the due time

    // Combine the date and time into a single datetime string
    $full_due_date = $due_date . ' ' . $due_time; // Combine date and time

    // Controleer of het titelveld leeg is
    if (empty($title)) {
        // Als het leeg is, stuur de gebruiker terug naar de startpagina met een foutmelding
        header("Location: ../index.php?mess=error");
    } else {
        // Bereid de SQL-query voor om een nieuwe taak in de database toe te voegen
        $sql = "INSERT INTO todos (title, due_date) VALUES (:title, :due_date)";
        $stmt = $conn->prepare($sql);

        // Bind de parameters
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':due_date', $full_due_date); // Bind the combined datetime

        // Voer de query uit
        $res = $stmt->execute();

        // Als de invoer succesvol is, stuur de gebruiker terug met een succesmelding
        if ($res) {
            header("Location: ../index.php?mess=success");
        } else {
            // Anders stuur de gebruiker terug zonder melding
            header("Location: ../index.php");
        }
        // Sluit de databaseverbinding
        $conn = null;
        exit();
    }
} else {
    // Als er geen titel is verzonden, stuur de gebruiker terug met een foutmelding
    header("Location: ../index.php?mess=error");
}
