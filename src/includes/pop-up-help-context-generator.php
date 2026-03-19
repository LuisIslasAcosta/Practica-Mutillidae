<?php

    if (session_status() == PHP_SESSION_NONE){
        session_start();
    }// end if

    /* ------------------------------------------
    * initialize SQL Query handler
    * ------------------------------------------ */
    require_once '../classes/SQLQueryHandler.php';
    $SQLQueryHandler = new SQLQueryHandler($_SESSION["security-level"]);

    /* ------------------------------------------
    * initialize custom error handler
    * ------------------------------------------ */
    require_once '../classes/CustomErrorHandler.php';
    $CustomErrorHandler = new CustomErrorHandler($_SESSION["security-level"]);

    try {
        // Sanitizar entrada para evitar XSS
        $lPageName = isset($_GET["pagename"]) ? htmlspecialchars($_GET["pagename"], ENT_QUOTES, 'UTF-8') : "";

        // Usar consultas preparadas para evitar SQL Injection
        $lQueryResult = $SQLQueryHandler->getPageHelpTexts($lPageName);

        echo '<div>&nbsp;</div>';

        if ($lQueryResult->num_rows > 0){
            echo '  <div class="help-text-header">
                    Hack with confidence.
                    <br/>
                    Page ' . $lPageName . ' is vulnerable to at least the following:</div>';

            while($row = $lQueryResult->fetch_object()){
                // Escapar salida para evitar XSS en help_text
                echo htmlspecialchars($row->help_text, ENT_QUOTES, 'UTF-8');
            }//end while $row
        }else{
            echo '  <div class="help-text-header">
                    Page ' . $lPageName . ' does not have any help documentation.</div>';
        }//end if

        echo '<div>&nbsp;</div>';

    } catch (Exception $e) {
        echo $CustomErrorHandler->FormatError($e, "Error selecting help text entries for page " . $lPageName);
    }// end try
?>
