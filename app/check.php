<?php

// Controleer of de 'id' is verzonden via het POST-verzoek
if(isset($_POST['id'])){
    // Inclusief databaseverbinding
    require '../db_conn.php';

    // Haal de id op uit het POST-verzoek
    $id = $_POST['id'];

    // Controleer of het id-veld leeg is
    if(empty($id)){
        // Als het leeg is, geef 'error' weer
        echo 'error';
    }else {
        // Bereid de SQL-query voor om de taak op te halen op basis van de id
        $todos = $conn->prepare("SELECT id, checked FROM todos WHERE id=?");
        // Voer de query uit met de opgegeven id
        $todos->execute([$id]);

        // Haal de taakgegevens op
        $todo = $todos->fetch();
        $uId = $todo['id'];
        $checked = $todo['checked'];

        // Toggle de status van 'checked' (1 wordt 0 en omgekeerd)
        $uChecked = $checked ? 0 : 1;

        // Voer de update-query uit om de status van de taak te wijzigen
        $res = $conn->query("UPDATE todos SET checked=$uChecked WHERE id=$uId");

        // Als de update succesvol is, geef de oude status weer
        if($res){
            echo $checked;
        }else {
            // Anders geef 'error' weer
            echo "error";
        }
        // Sluit de databaseverbinding
        $conn = null;
        exit();
    }
}else {
    // Als er geen id is verzonden, stuur de gebruiker terug met een foutmelding
    header("Location: ../index.php?mess=error");
}
