<?php
    try {
        require_once './includes/constants.php';

        if (session_status() === PHP_SESSION_NONE && !headers_sent()){
            session_start();
        }

        if (!isset($_SESSION["security-level"])){
            $_SESSION["security-level"] = 0;
        }

        require_once __SITE_ROOT__.'/classes/CustomErrorHandler.php';
        if (!isset($CustomErrorHandler)){
            $CustomErrorHandler = new CustomErrorHandler($_SESSION["security-level"]);
        }

        require_once __SITE_ROOT__.'/classes/SQLQueryHandler.php';
        $SQLQueryHandler = new SQLQueryHandler($_SESSION["security-level"]);

        require_once __SITE_ROOT__.'/classes/YouTubeVideoHandler.php';
        $YouTubeVideoHandler = new YouTubeVideoHandler($_SESSION["security-level"]);

        // Validar entrada: solo números enteros permitidos
        if (isset($_REQUEST["level1HintIncludeFile"]) && ctype_digit($_REQUEST["level1HintIncludeFile"])) {
            $lIncludeFileKey = (int) $_REQUEST["level1HintIncludeFile"];
        } else {
            $lIncludeFileKey = 52;
        }

        // Consultas preparadas en SQLQueryHandler
        $lIncludeFileRecord = $SQLQueryHandler->getLevelOneHelpIncludeFileSafe($lIncludeFileKey);

        if ($SQLQueryHandler->affected_rows() > 0) {
            $lRecord = $lIncludeFileRecord->fetch_object();
            $lIncludeFile = basename($lRecord->level_1_help_include_file);
            $lIncludeFileDescription = htmlspecialchars($lRecord->level_1_help_include_file_description, ENT_QUOTES, 'UTF-8');
        } else {
            $lIncludeFile = 'hint-not-found.inc';
            $lIncludeFileDescription = 'Hint Not Found';
        }

    } catch (Exception $e) {
        echo $CustomErrorHandler->FormatError($e, "Error selecting help text entries");
    }
?>

<!DOCTYPE html>
<html lang="en" xml:lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="./styles/global-styles.css" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo $lIncludeFileDescription; ?></title>
    </head>
    <body>
        <table class="hint-table">
            <tr class="hint-header">
                <td><?php echo $lIncludeFileDescription; ?></td>
            </tr>
            <tr>
                <td class="hint-body">
                    <?php 
                        $allowedPath = realpath(__DIR__ . '/includes/hints/' . $lIncludeFile);
                        $hintsDir = realpath(__DIR__ . '/includes/hints/');
                        if ($allowedPath !== false && strpos($allowedPath, $hintsDir) === 0) {
                            include_once $allowedPath;
                        } else {
                            echo "Invalid file path.";
                        }
                    ?>
                </td>
            </tr>
        </table>
    </body>
</html>
