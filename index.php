<?php
// Verbind met de database
require 'db_conn.php';
// Importeer de navigatiebalk (navbar)
require 'layouts/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Laad FontAwesome voor iconen -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>To-Do List</title>
    <!-- Voeg een stylesheet toe met een queryparameter voor cache-busting -->
    <link rel="stylesheet" href="css/style.css?v=<?php echo time();?>">
</head>
<body>
<div class="main-section">
    <div class="add-section">
        <!-- Formulier om een nieuw to-do item toe te voegen -->
        <form action="app/add.php" method="POST" autocomplete="off">

            <!-- Controleer of er een foutmelding is en toon een foutbericht -->
            <?php if(isset($_GET['mess']) && $_GET['mess'] == 'error'){ ?>
                <input type="text"
                       name="title"
                       style="border-color: #ff6666"
                       placeholder="Dit veld is verplicht" />
                <input type="text" id="due_date" name="due_date" placeholder="Kies een datum" required />
                <input type="time" id="due_time" name="due_time" required />
                <button type="submit"><i class="fa-solid fa-square-plus"></i></button>

            <?php }else{ ?>
                    <p class="ml-4">TODO</p>
                <input type="text"
                       name="title"
                       placeholder="Wat moet je doen?" />
                <p class="ml-4">Tijd</p>

                <input type="text" id="due_date" name="due_date" placeholder="Kies een datum" required />

                <input type="time" id="due_time" name="due_time" required />
                <button type="submit"><i class="fa-solid fa-square-plus"></i></button>
            <?php } ?>
        </form>
    </div>
    <?php
    // Haal alle to-do items op uit de database, gesorteerd op id in aflopende volgorde
    $todos = $conn->query("SELECT * FROM todos ORDER BY id DESC");
    ?>
    <div class="show-todo-section">
        <!-- Als er geen to-do items zijn, toon een leeg bericht -->
        <?php if($todos->rowCount() <= 0){ ?>
            <div class="todo-item">
                <div class="empty">

                </div>
            </div>
        <?php } ?>

        <!-- Toon alle to-do items uit de database -->
        <?php while($todo = $todos->fetch(PDO::FETCH_ASSOC)) { ?>
            <div class="todo-item">
                <!-- Verwijderknop voor to-do item -->
                <span id="<?php echo $todo['id']; ?>"
                      class="remove-to-do"><i class="fa-solid fa-trash" ></i></span>
                <!-- Als het item is voltooid, toon het als afgevinkt -->
                <?php if($todo['checked']){ ?>
                    <label>
                        <input type="checkbox"
                               class="check-box"
                               data-todo-id ="<?php echo $todo['id']; ?>"
                               checked />
                        <h2 class="checked"><?php echo $todo['title'] ?></h2>

                    </label>
                <?php }else { ?>
                    <input type="checkbox"
                           data-todo-id ="<?php echo $todo['id']; ?>"
                           class="check-box" />
                    <h2><?php echo $todo['title'] ?></h2>
                <?php } ?>
                <br>
                <small>Time to finish the task: <?php echo $todo['due_date'] ?></small>
                <small>Task creation time : <?php echo $todo['date_time'] ?></small>

            </div>
        <?php } ?>
    </div>
</div>

<!-- Laad jQuery om de interacties met de server te beheren -->
<script src="js/jquery-3.2.1.min.js"></script>

<script>
    $(document).ready(function() {
        $("#due_date").datepicker({
            dateFormat: "yy-mm-dd"
        });
    });

    $(document).ready(function() {
        // Verwijder een to-do item wanneer op het verwijder icoon wordt geklikt
        $('.remove-to-do').click(function() {
            const id = $(this).attr('id'); // Haal het ID op van het to-do item

            // Verstuur een POST-verzoek naar remove.php om het item te verwijderen
            $.post("app/remove.php", { id: id }, (data) => {
                if (data == '1') {
                    // Als de verwijdering succesvol was, verberg het item met een animatie
                    $(this).parent().hide(600);
                    alert('To-do item succesvol verwijderd!'); // Succesbericht
                } else {
                    // Als er een fout was, laat een foutmelding zien
                    alert('Er is een fout opgetreden bij het verwijderen van het to-do item.');
                }
            }).fail(() => {
                // Handle AJAX request failure
                alert('Er was een probleem met de aanvraag. Probeer het later opnieuw.');
            });
        });

        // Markeer een to-do item als voltooid of onvoltooid wanneer op de checkbox wordt geklikt
        $(".check-box").click(function(e) {
            const id = $(this).attr('data-todo-id'); // Haal het ID op van het to-do item

            // Verstuur een POST-verzoek naar check.php om de status bij te werken
            $.post('app/check.php', { id: id }, (data) => {
                if (data != 'error') {
                    const h2 = $(this).next(); // Selecteer het bijbehorende h2-element
                    if (data === '1') {
                        h2.removeClass('checked'); // Verwijder de "checked" klasse als het item onvoltooid is
                    } else {
                        h2.addClass('checked'); // Voeg de "checked" klasse toe als het item voltooid is
                    }
                } else {
                    alert('Er was een fout bij het bijwerken van de to-do status.');
                }
            }).fail(() => {
                alert('Er was een probleem met de aanvraag. Probeer het later opnieuw.');
            });
        });
    });



</script>


<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

</body>
</html>
